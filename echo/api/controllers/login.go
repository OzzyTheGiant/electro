package controllers

import (
	db "electro/api/database"
	ctx "electro/api/middleware"
	"electro/api/models"
	"net/http"
	"os"

	"github.com/alexedwards/argon2id"
	"github.com/golang-jwt/jwt/v5"
	"github.com/labstack/echo/v4"
)

type Credentials struct {
	Username string `json:"username"`
	Password string `json:"password"`
}

func Login(context echo.Context) (err error) {
	var credentials Credentials
	var user models.User

	if err = (context.(*ctx.AppContext)).BindAndValidate(&credentials); err != nil {
		return context.JSON(http.StatusBadRequest, echo.Map{
			"message": "Could not read credentials",
		})
	}

	dao := context.Get("dao").(*db.DBAccessObject)
	err = dao.SelectUserByUsername(credentials.Username, &user)

	if user.Username == "" {
		return context.JSON(http.StatusUnauthorized, echo.Map{
			"message": "Username or password is incorrect",
		})
	}

	if err != nil {
		return context.JSON(http.StatusInternalServerError, err.Error())
	}

	match, err := argon2id.ComparePasswordAndHash(credentials.Password, *user.Password)

	if err != nil {
		context.Logger().Error(err.Error())
		return context.JSON(http.StatusInternalServerError, echo.Map{
			"message": "There was a problem verifying your credentials",
		})
	}

	if !match {
		return context.JSON(http.StatusUnauthorized, echo.Map{
			"message": "Username or password is not correct",
		})
	}

	err = createJWTToken(&user, context)

	if err != nil {
		context.Logger().Error(err.Error())
		return context.JSON(http.StatusInternalServerError, echo.Map{
			"message": "There was a problem while trying to log in",
		})
	}

	user.Password = nil
	return context.JSON(http.StatusOK, &user)
}

func Logout(context echo.Context) error {
	deleteCookie(context, os.Getenv("JWT_ACCESS_COOKIE_NAME"))
	return context.NoContent(http.StatusNoContent)
}

func createJWTToken(user *models.User, context echo.Context) error {
	token := jwt.NewWithClaims(jwt.SigningMethodHS256, jwt.MapClaims{
		"username": user.Username,
	})

	tokenString, err := token.SignedString([]byte(os.Getenv("APP_KEY")))

	if err == nil {
		createCookie(context, os.Getenv("JWT_ACCESS_COOKIE_NAME"), tokenString)
	}

	return err
}
