package database

import (
	"fmt"
	"os"

	"github.com/labstack/gommon/log"
	"gorm.io/driver/postgres"
	"gorm.io/gorm"
)

type DBAccessObject struct {
	db     *gorm.DB
	logger *log.Logger
}

func InitService(logger *log.Logger) *DBAccessObject {
	dsn := fmt.Sprintf(
		"host=%s port=%s dbname=%s user=%s password=%s sslmode=disable",
		os.Getenv("DB_HOST"),
		os.Getenv("DB_PORT"),
		os.Getenv("DB_DATABASE"),
		os.Getenv("DB_USER"),
		os.Getenv("DB_PASSWORD"),
	)

	fmt.Println(os.Getenv("DB_USER"))

	db, err := gorm.Open(postgres.Open(dsn), &gorm.Config{})

	if err != nil {
		log.Fatal("Connection to PostgreSQL database could not be opened")
	}

	return &DBAccessObject{db, logger}
}

func (dao *DBAccessObject) beginTransaction() (db *gorm.DB) {
	db = dao.db
	tx := db.Begin()
	dao.setDB(tx)
	return
}

func (dao *DBAccessObject) setDB(db *gorm.DB) {
	dao.db = db
}
