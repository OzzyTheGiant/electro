package services

import (
	"io"
	"os"

	"github.com/labstack/gommon/log"
	"gopkg.in/natefinch/lumberjack.v2"
)

func InitLogger() *log.Logger {
	var handler io.Writer

	logFile := &lumberjack.Logger{
		Filename:   os.Getenv("LOG_FILE_PATH"),
		MaxSize:    20, // MB
		MaxBackups: 3,
		MaxAge:     28, // days
		Compress:   true,
	}

	handler = io.MultiWriter(os.Stdout, logFile)
	logger := log.New("echo")

	logger.SetOutput(handler)
	logger.SetHeader(
		`[${time_rfc3339_nano}] ${prefix} ${level} ` +
			`file=${long_file}, line=${line}`,
	)

	if os.Getenv("APP_ENV") == "development" {
		logger.SetLevel(log.DEBUG)
	} else {
		logger.SetLevel(log.INFO)
	}

	return logger
}
