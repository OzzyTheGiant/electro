package middleware

import (
	"os"

	"github.com/labstack/echo/v4"
	"github.com/labstack/echo/v4/middleware"
)

func csrfMiddleware() echo.MiddlewareFunc {
	return middleware.CSRFWithConfig(middleware.CSRFConfig{
		TokenLookup:    "header:" + os.Getenv("CSRF_HEADER_NAME"),
		CookieName:     os.Getenv("JWT_CSRF_COOKIE_NAME"),
		CookiePath:     "/",
		CookieMaxAge:   28800, // 8 hours
		CookieSecure:   os.Getenv("APP_ENV") == "production",
		CookieHTTPOnly: false,
	})
}
