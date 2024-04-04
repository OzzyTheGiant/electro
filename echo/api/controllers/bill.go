package controllers

import (
	db "electro/api/database"
	ctx "electro/api/middleware"
	"electro/api/models"
	"net/http"

	"github.com/labstack/echo/v4"
)

const BILL_URL = "/bills"

func FetchBills(context echo.Context) error {
	var bills = []models.Bill{}

	dao := context.Get("dao").(*db.DBAccessObject)
	err := dao.SelectBills(&bills)

	if err != nil {
		return context.JSON(http.StatusInternalServerError, echo.Map{"message": err.Error()})
	}

	return context.JSON(http.StatusOK, bills)
}

func CreateBill(context echo.Context) error {
	var bills models.Bill
	var err error

	if err = (context.(*ctx.AppContext)).BindAndValidate(&bills); err != nil {
		return context.JSON(http.StatusBadRequest, echo.Map{"message": err.Error()})
	}

	dao := context.Get("dao").(*db.DBAccessObject)

	if err = dao.InsertBill(&bills); err != nil {
		return context.JSON(http.StatusInternalServerError, echo.Map{"message": err.Error()})
	}

	return context.JSON(http.StatusCreated, bills)
}

func UpdateBill(context echo.Context) error {
	var bill models.Bill
	var err error

	if err = (context.(*ctx.AppContext)).BindAndValidate(&bill); err != nil {
		return context.JSON(http.StatusBadRequest, echo.Map{"message": err.Error()})
	}

	dao := context.Get("dao").(*db.DBAccessObject)
	rowsAffected, err := dao.UpdateBill(&bill)

	if err != nil {
		return context.JSON(http.StatusInternalServerError, echo.Map{"message": err.Error()})
	} else if rowsAffected == 0 {
		return context.JSON(http.StatusNotFound, echo.Map{"message": "Bill not found"})
	}

	return context.JSON(http.StatusOK, bill)
}

func DeleteBill(context echo.Context) error {
	idList, err := createParamListFromString(context.Param("id"))

	if err != nil {
		return context.JSON(http.StatusBadRequest, echo.Map{"message": err})
	}

	dao := context.Get("dao").(*db.DBAccessObject)
	rowsAffected, err := dao.DeleteBill(idList)

	if err != nil {
		return context.JSON(http.StatusInternalServerError, echo.Map{"message": err.Error()})
	} else if rowsAffected == 0 {
		return context.JSON(http.StatusNotFound, echo.Map{"message": "Bill not found"})
	}

	return context.NoContent(http.StatusNoContent)
}
