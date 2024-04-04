package database

import (
	"fmt"
	"os"

	"github.com/labstack/gommon/log"
	"gorm.io/driver/postgres"
	"gorm.io/driver/sqlite"
	"gorm.io/gorm"
)

type DBAccessObject struct {
	db     *gorm.DB
	logger *log.Logger
}

func (dao *DBAccessObject) GetDB() *gorm.DB {
	return dao.db
}

func InitService(logger *log.Logger) *DBAccessObject {
	var dsn string
	var db *gorm.DB
	var err error

	if os.Getenv("DB_CONNECTION") != "sqlite" {
		dsn = fmt.Sprintf(
			"host=%s port=%s dbname=%s user=%s password=%s sslmode=disable",
			os.Getenv("DB_HOST"),
			os.Getenv("DB_PORT"),
			os.Getenv("DB_DATABASE"),
			os.Getenv("DB_USER"),
			os.Getenv("DB_PASSWORD"),
		)
	}

	if dsn == "" {
		db, err = gorm.Open(sqlite.Open("file::memory:?cache=shared"), &gorm.Config{})
	} else {
		db, err = gorm.Open(postgres.Open(dsn), &gorm.Config{})
	}

	if err != nil {
		log.Fatal("Connection to PostgreSQL database could not be opened")
	}

	return &DBAccessObject{db, logger}
}
