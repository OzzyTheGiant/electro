package middleware

import (
	"electro/api/database"
	"electro/api/models"
	"net/http"
	"os"
	"strings"

	"github.com/golang-jwt/jwt/v5"
	echo_jwt "github.com/labstack/echo-jwt/v4"
	"github.com/labstack/echo/v4"
)

func jwtValidationMiddleware() echo.MiddlewareFunc {
	return echo_jwt.WithConfig(echo_jwt.Config{
		SigningKey:             []byte(os.Getenv("APP_KEY")),
		ContextKey:             "user",
		ContinueOnIgnoredError: false,
		TokenLookup:            "cookie:" + os.Getenv("JWT_ACCESS_COOKIE_NAME"),
		Skipper:                isPublicRoute,
		ErrorHandler:           onJWTValidationFailed,
		SuccessHandler:         onJWTValidationSuccess,
	})
}

func jwtAuthenticationMiddleware(next echo.HandlerFunc) echo.HandlerFunc {
	return func(context echo.Context) error {
		if isPublicRoute(context) {
			return next(context)
		}

		var user models.User

		dao := context.Get("dao").(*database.DBAccessObject)
		userToken := context.Get("user").(*jwt.Token)
		claims := userToken.Claims.(jwt.MapClaims)
		username := claims["username"].(string)
		err := dao.SelectUserByUsername(username, &user)

		if err != nil {
			return context.JSON(http.StatusUnauthorized, echo.Map{
				"message": "Unauthorized to perform this action",
			})
		}

		return next(context)
	}
}

// This function is for excluding routes with static files from JWT Authentication. Useful
// for situations in which you want to embed static files in your app.
func isPublicRoute(context echo.Context) bool {
	route := context.Request().URL.String()
	safeRoutes := []string{
		".js",
		".css",
		".html",
		".jpg",
		".png",
		".svg",
		".ico",
		".xml",
		".txt",
		".json",
		".woff",
		".woff2",
		".webmanifest",
		"/home",
		"/login",
		"/logout",
	}

	for _, safeRoute := range safeRoutes {
		if route == "/" || strings.HasSuffix(route, safeRoute) {
			return true
		}
	}

	return false
}

func onJWTValidationSuccess(context echo.Context) {
	if os.Getenv("APP_ENV") == "development" {
		context.Logger().Info("JWT verified successfully")
	}
}

func onJWTValidationFailed(context echo.Context, err error) error {
	return context.JSON(http.StatusUnauthorized, echo.Map{
		"message": "Unauthorized or session expired",
	})
}
