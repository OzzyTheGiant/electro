package main

import (
	"electro/api/controllers"
	"electro/api/middleware"

	_ "github.com/joho/godotenv/autoload"
	"github.com/labstack/echo/v4"
)

const BASE_URL = "/api"

func main() {
	app := echo.New()
	middleware.RegisterServicesAndMiddleware(app)
	controllers.RegisterAppRoutes(BASE_URL, app)
	app.Logger.Fatal(app.Start(":1323"))
}
