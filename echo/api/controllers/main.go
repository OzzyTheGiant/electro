package controllers

import (
	"errors"
	"net/http"
	"os"
	"strconv"
	"strings"
	"time"

	"github.com/labstack/echo/v4"
)

func RegisterAppRoutes(BASE_URL string, e *echo.Echo) {
	e.GET(BASE_URL+"/home", home)
	e.POST(BASE_URL+"/login", login)
	e.POST(BASE_URL+"/logout", logout)
	e.GET(BASE_URL+BILL_URL, fetchBills)
	e.POST(BASE_URL+BILL_URL, createBill)
	e.PUT(BASE_URL+BILL_URL, updateBills)
	e.DELETE(BASE_URL+BILL_URL+"/:id", deleteBill)
}

func home(context echo.Context) error {
	return context.NoContent(http.StatusNoContent)
}

func createParamListFromString(paramString string) ([]int64, error) {
	idList := []int64{}
	errMessage := errors.New("The specified tax return's ID is invalid")

	if strings.Contains(paramString, "-") {
		params := strings.Split(paramString, "-")

		for _, param := range params {
			value, err := strconv.ParseInt(param, 10, 32)

			if err != nil {
				return nil, errMessage
			}

			idList = append(idList, value)
		}
	} else {
		id, err := strconv.ParseInt(paramString, 10, 32)

		if err != nil {
			return nil, errMessage
		}

		idList = append(idList, id)
	}

	return idList, nil
}

func createCookie(context echo.Context, name string, value string) {
	var lifetime time.Duration = 0

	cookie := new(http.Cookie)
	cookie.Name = name
	cookie.Value = value
	cookie.Secure = os.Getenv("APP_ENV") == "production"
	cookie.HttpOnly = true

	expTime, err := strconv.ParseInt(os.Getenv("JWT_ACCESS_TOKEN_EXPIRES"), 10, 64)

	if err != nil {
		lifetime = time.Duration(24)
	} else {
		lifetime = time.Duration(expTime)
	}

	cookie.Expires = time.Now().Add(time.Hour * lifetime)

	context.SetCookie(cookie)
}

func deleteCookie(context echo.Context, name string) {
	cookie := new(http.Cookie)
	cookie.Name = name
	cookie.Value = ""
	cookie.MaxAge = -1
	cookie.Expires = time.Now().Add(-8 * time.Hour)
	cookie.HttpOnly = true
	context.SetCookie(cookie)
}
