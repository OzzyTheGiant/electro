package middleware

import (
	"context"
	"log/slog"
	"os"

	"github.com/labstack/echo/v4"
	"github.com/labstack/echo/v4/middleware"
)

// Outputs data for each request in a development environment
func loggerMiddleware(basic bool) echo.MiddlewareFunc {
	if basic {
		return middleware.LoggerWithConfig(middleware.LoggerConfig{
			Format: "[${time_rfc3339_nano}] ${method} ${uri} | Status: ${status}\n",
		})
	}

	return middleware.RequestLoggerWithConfig(middleware.RequestLoggerConfig{
		LogStatus:     true,
		LogURI:        true,
		LogError:      true,
		HandleError:   true, // forwards error to the global error handler, so it can decide appropriate status code
		LogValuesFunc: logRequest,
		Skipper:       shouldSkipLoggerMiddleware,
	})
}

// A function for outputting formatted request data
func logRequest(c echo.Context, v middleware.RequestLoggerValues) error {
	logger := c.Get("logger").(*slog.Logger)

	if v.Error == nil {
		logger.LogAttrs(context.Background(), slog.LevelInfo, "REQUEST",
			slog.String("uri", v.URI),
			slog.Int("status", v.Status),
		)
	} else {
		logger.LogAttrs(context.Background(), slog.LevelError, "REQUEST_ERROR",
			slog.String("uri", v.URI),
			slog.Int("status", v.Status),
			slog.String("err", v.Error.Error()),
		)
	}

	return nil
}

func shouldSkipLoggerMiddleware(context echo.Context) bool {
	return context.Request().Method == "OPTIONS" || os.Getenv("APP_ENV") == "production"
}
