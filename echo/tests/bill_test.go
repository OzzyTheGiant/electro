package test

import (
	"bytes"
	"encoding/json"
	"net/http"
	"net/http/httptest"
	"testing"

	"github.com/labstack/echo/v4"
	"github.com/stretchr/testify/assert"

	"electro/api/controllers"
	"electro/api/database"
	"electro/api/middleware"
	"electro/api/models"
)

func CreateSampleBill(services middleware.ServiceMap) (bill models.Bill) {
	bill = models.Bill{
		UserID:        1,
		PaymentAmount: 55.55,
		PaymentDate:   "2024-04-01T00:00:00Z",
	}

	db := (*services)["dao"].(*database.DBAccessObject).GetDB()
	db.FirstOrCreate(&bill)

	return
}

func TestFetchBills(t *testing.T) {
	app, services, logger := SetUp()
	bill := CreateSampleBill(services)

	req := httptest.NewRequest(http.MethodGet, BASE_URL+controllers.BILL_URL, nil)
	recorder := httptest.NewRecorder()
	context := middleware.CreateCustomContext(app.NewContext(req, recorder), services, logger)

	if assert.NoError(t, controllers.FetchBills(context)) {
		var data []map[string]interface{}
		json.Unmarshal(recorder.Body.Bytes(), &data)

		assert.Equal(t, http.StatusOK, recorder.Code)
		assert.Equal(t, 1, len(data))
		assert.Equal(t, bill.ID, int(data[0]["id"].(float64)))
		assert.Equal(t, bill.PaymentAmount, float32(data[0]["payment_amount"].(float64)))
		assert.Equal(t, bill.PaymentDate, data[0]["payment_date"])
	}
}

func TestCreateBill(t *testing.T) {
	app, services, logger := SetUp()
	db := (*services)["dao"].(*database.DBAccessObject).GetDB()

	bill := models.Bill{
		UserID:        1,
		PaymentAmount: 55.55,
		PaymentDate:   "2024-04-01",
	}

	billJSON, _ := json.Marshal(bill)
	url := BASE_URL + controllers.BILL_URL
	req := httptest.NewRequest(http.MethodPost, url, bytes.NewReader(billJSON))
	req.Header.Set(echo.HeaderContentType, echo.MIMEApplicationJSON)
	recorder := httptest.NewRecorder()
	context := middleware.CreateCustomContext(app.NewContext(req, recorder), services, logger)

	if assert.NoError(t, controllers.CreateBill(context)) {
		var data map[string]interface{}
		json.Unmarshal(recorder.Body.Bytes(), &data)

		assert.Equal(t, http.StatusCreated, recorder.Code)
		assert.Equal(t, bill.UserID, int(data["user_id"].(float64)))
		assert.Equal(t, bill.PaymentAmount, float32(data["payment_amount"].(float64)))
		assert.Equal(t, bill.PaymentDate, data["payment_date"])

		bills := []models.Bill{}
		db.Find(&bills)
		assert.Greater(t, len(bills), 0)
		assert.Equal(t, 1, bills[0].ID)
	}
}

func TestUpdateBill(t *testing.T) {
	app, services, logger := SetUp()
	db := (*services)["dao"].(*database.DBAccessObject).GetDB()
	CreateSampleBill(services)

	bill := models.Bill{
		ID:            1,
		UserID:        1,
		PaymentAmount: 66.66,
		PaymentDate:   "2024-04-01",
	}

	billJSON, _ := json.Marshal(bill)
	url := BASE_URL + controllers.BILL_URL
	req := httptest.NewRequest(http.MethodPut, url, bytes.NewReader(billJSON))
	req.Header.Set(echo.HeaderContentType, echo.MIMEApplicationJSON)
	recorder := httptest.NewRecorder()
	context := middleware.CreateCustomContext(app.NewContext(req, recorder), services, logger)

	if assert.NoError(t, controllers.UpdateBill(context)) {
		var data map[string]interface{}
		json.Unmarshal(recorder.Body.Bytes(), &data)

		assert.Equal(t, http.StatusOK, recorder.Code)
		assert.Equal(t, bill.ID, int(data["id"].(float64)))
		assert.Equal(t, bill.UserID, int(data["user_id"].(float64)))
		assert.Equal(t, bill.PaymentAmount, float32(data["payment_amount"].(float64)))
		assert.Equal(t, bill.PaymentDate, data["payment_date"])

		row := models.Bill{}
		db.First(&row)
		assert.Equal(t, row.PaymentAmount, float32(data["payment_amount"].(float64)))
	}
}

func TestDeleteBill(t *testing.T) {
	app, services, logger := SetUp()
	db := (*services)["dao"].(*database.DBAccessObject).GetDB()
	CreateSampleBill(services)

	req := httptest.NewRequest(http.MethodDelete, BASE_URL+controllers.BILL_URL+"/1", nil)
	recorder := httptest.NewRecorder()
	context := middleware.CreateCustomContext(app.NewContext(req, recorder), services, logger)
	context.SetParamNames("id")
	context.SetParamValues("1")

	if assert.NoError(t, controllers.DeleteBill(context)) {
		assert.Equal(t, http.StatusNoContent, recorder.Code)
		assert.Equal(t, recorder.Body.String(), "")

		bills := []models.Bill{}
		db.Find(&bills)
		assert.GreaterOrEqual(t, 1, len(bills))
	}
}
