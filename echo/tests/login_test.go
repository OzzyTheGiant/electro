package test

import (
	"bytes"
	"encoding/json"
	"fmt"
	"net/http"
	"net/http/httptest"
	"testing"

	"github.com/joho/godotenv"
	"github.com/labstack/echo/v4"
	"github.com/labstack/gommon/log"
	"github.com/stretchr/testify/assert"

	"electro/api/controllers"
	"electro/api/database"
	"electro/api/middleware"
	"electro/api/models"
)

var BASE_URL = "/api"

func SetUp() (app *echo.Echo, services middleware.ServiceMap, logger *log.Logger) {
	godotenv.Load("../.env")
	app = echo.New()

	// NOTE: Ensure that DB_CONNECTION env variable is set to "sqlite"
	services, logger = middleware.RegisterServicesAndMiddleware(app)
	controllers.RegisterAppRoutes(BASE_URL, app)

	dao := (*services)["dao"].(*database.DBAccessObject)
	db := dao.GetDB()

	db.AutoMigrate(&models.User{}, &models.Bill{})
	pw := "$argon2id$v=19$m=65536,t=3,p=4$ZeYABY+RVIf42Dx31DVhwg$Q4JkBb+fAIuFRq0ZN4b3GnP05U6Nw7XQWDTkBR2rtyk"

	db.FirstOrCreate(&models.User{
		ID:       1,
		Username: "OzzyTheGiant",
		Password: &pw,
	})

	return
}

func TestLoginUnsuccessfully(t *testing.T) {
	app, services, logger := SetUp()

	credentials := controllers.Credentials{
		Username: "OzzyTheGiant",
		Password: "fakepassword",
	}

	billJSON, _ := json.Marshal(credentials)
	url := BASE_URL + controllers.BILL_URL
	req := httptest.NewRequest(http.MethodPost, url, bytes.NewReader(billJSON))
	req.Header.Set(echo.HeaderContentType, echo.MIMEApplicationJSON)
	recorder := httptest.NewRecorder()
	context := middleware.CreateCustomContext(app.NewContext(req, recorder), services, logger)

	if assert.NoError(t, controllers.Login(context)) {
		var data map[string]interface{}
		json.Unmarshal(recorder.Body.Bytes(), &data)

		assert.Equal(t, http.StatusUnauthorized, recorder.Code)
		assert.NotEmpty(t, data["message"])
	}
}

func TestLoginSuccessfully(t *testing.T) {
	app, services, logger := SetUp()

	credentials := controllers.Credentials{
		Username: "OzzyTheGiant",
		Password: "notarealpassword",
	}

	billJSON, _ := json.Marshal(credentials)
	url := BASE_URL + "/login"
	req := httptest.NewRequest(http.MethodPut, url, bytes.NewReader(billJSON))
	req.Header.Set(echo.HeaderContentType, echo.MIMEApplicationJSON)
	recorder := httptest.NewRecorder()
	context := middleware.CreateCustomContext(app.NewContext(req, recorder), services, logger)

	if assert.NoError(t, controllers.Login(context)) {
		var data map[string]interface{}
		json.Unmarshal(recorder.Body.Bytes(), &data)
		fmt.Println(data)

		assert.Equal(t, http.StatusOK, recorder.Code)
		assert.Equal(t, credentials.Username, data["username"])
	}
}

func TestLogout(t *testing.T) {
	app, services, logger := SetUp()

	req := httptest.NewRequest(http.MethodPost, BASE_URL+"/logout", nil)
	recorder := httptest.NewRecorder()
	context := middleware.CreateCustomContext(app.NewContext(req, recorder), services, logger)

	if assert.NoError(t, controllers.Logout(context)) {
		assert.Equal(t, http.StatusNoContent, recorder.Code)
		assert.Equal(t, recorder.Body.String(), "")
	}
}
