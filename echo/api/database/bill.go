package database

import (
	"electro/api/models"
	"errors"
)

func (dao *DBAccessObject) SelectBills(bills *[]models.Bill) (err error) {
	result := dao.db.Order("payment_date").Find(&bills)

	if err := result.Error; err != nil {
		dao.logger.Error(err.Error())
		return errors.New("[Server Error]: Could not fetch bill data")
	}

	return
}

func (dao *DBAccessObject) InsertBill(bills *models.Bill) (err error) {
	if err = dao.db.Create(&bills).Error; err != nil {
		dao.logger.Error(err.Error())
		return errors.New("[Server Error]: Could not create bill")
	}

	return
}

func (dao *DBAccessObject) UpdateBill(bills *models.Bill) (rowCount int64, err error) {
	result := dao.db.Model(&bills).Updates(bills.ToMap())

	rowCount = result.RowsAffected

	if err = result.Error; err != nil || rowCount == 0 {
		dao.logger.Error(err.Error())
		return rowCount, errors.New("[Server Error]: Could not update bill")
	}

	return
}

func (dao *DBAccessObject) DeleteBill(idList []int64) (rowsAffected int64, err error) {
	result := dao.db.Delete(&models.Bill{}, idList)
	rowsAffected = result.RowsAffected

	if err = result.Error; err != nil {
		dao.logger.Error(err.Error())
		return rowsAffected, errors.New("[Server Error]: Could not delete bills")
	}

	return
}
