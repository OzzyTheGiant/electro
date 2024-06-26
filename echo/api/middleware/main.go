package middleware

import (
	"electro/api/database"
	s "electro/api/services"
	"fmt"

	"github.com/go-playground/validator/v10"
	"github.com/labstack/echo/v4"
	"github.com/labstack/echo/v4/middleware"
	"github.com/labstack/gommon/log"
)

// This is a custom app context that can be used to add extension methods
type AppContext struct {
	echo.Context
}

type ServiceMap = *map[string]interface{}

func RegisterServicesAndMiddleware(e *echo.Echo) (services ServiceMap, logger *log.Logger) {
	logger = s.InitLogger()
	services = &map[string]interface{}{
		"dao":       database.InitService(logger),
		"validator": validator.New(validator.WithRequiredStructEnabled()),
	}

	e.Use(customContextMiddleware(services, logger))
	e.Use(loggerMiddleware(true))
	e.Use(middleware.Recover())
	e.Use(csrfMiddleware())
	e.Use(jwtValidationMiddleware())
	e.Use(jwtAuthenticationMiddleware)

	return
}

func CreateCustomContext(
	context echo.Context,
	services *map[string]interface{},
	logger *log.Logger,
) *AppContext {
	appContext := &AppContext{context}
	appContext.SetLogger(logger)

	for key, value := range *services {
		appContext.Set(key, value)
	}

	return appContext
}

func customContextMiddleware(
	services *map[string]interface{},
	logger *log.Logger,
) echo.MiddlewareFunc {
	return func(next echo.HandlerFunc) echo.HandlerFunc {
		return func(context echo.Context) error {
			return next(CreateCustomContext(context, services, logger))
		}
	}
}

func (context *AppContext) BindAndValidate(model interface{}) (err error) {
	err = context.Bind(model)

	if err != nil {
		return
	}

	validate := context.Get("validator").(*validator.Validate)
	err = validate.Struct(model)

	if err == nil {
		return
	}

	validationErrors := err.(validator.ValidationErrors)

	return fmt.Errorf(
		`[Validation Error]: %s field failed the "%s" rule`,
		validationErrors[0].StructField(),
		validationErrors[0].Tag(),
	)
}
