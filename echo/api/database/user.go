package database

import (
	"electro/api/models"
	"errors"
)

func (dao *DBAccessObject) SelectUserByUsername(username string, user *models.User) (err error) {
	result := dao.db.Where("username = ?", username).First(&user)

	if err := result.Error; err != nil {
		dao.logger.Error(err.Error())
		return errors.New("Server Error: Could not fetch user data")
	}

	return nil
}

func (dao *DBAccessObject) SelectAllUsers(users *[]models.User) (err error) {
	result := dao.db.Find(&users)

	if err := result.Error; err != nil {
		dao.logger.Error(err.Error())
		return errors.New("Server Error: Could not fetch user data")
	}

	return
}
