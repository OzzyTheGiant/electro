import os
import sys
import base64
import logging
from datetime import timedelta
from logging.config import dictConfig

environment = os.getenv("APP_ENV")

config = {
    "ENV": "development" if environment == "local" else environment,
    "SECRET_KEY": base64.b64decode(os.getenv("APP_KEY").split(":")[1]),
    "SESSION_COOKIE_NAME": os.getenv("SESSION_COOKIE"),
    "SESSION_COOKIE_SECURE": environment != "local",
    # multiply minutes times seconds
    "PERMANENT_SESSION_LIFETIME": timedelta(seconds=int(os.getenv("SESSION_LIFETIME")) * 60),
    "MAX_CONTENT_LENGTH": 1048576,  # 1 MB
    "SESSION_REFRESH_EACH_REQUEST": True,
    "WTF_CSRF_HEADERS": ['X-XSRF-TOKEN', 'X-CSRF-TOKEN']
}


def configure_logging():
    dictConfig({
        "version": 1,
        "formatters": {
            'standard': {
                'class': 'logging.Formatter',
                'format': '[%(asctime)s] %(levelname)s - %(module)s: %(message)s; Metadata:%(args)s'
            },
            'colored': {
                'class': 'colorlog.ColoredFormatter',
                'format': '[%(asctime)s] %(log_color)s%(levelname)s%(reset)s - %(module)s: %(message)s; Metadata:%(args)s'  # noqa: E501
            }
        },
        'handlers': {
            'console': {
                "class": (
                    "logging.StreamHandler"
                    if environment != 'local'
                    else "colorlog.StreamHandler"
                ),
                "stream": sys.stdout,
                "formatter": "standard" if environment != 'local' else 'colored',
                "level": logging.DEBUG
            },
            'file': {
                "class": "logging.FileHandler",
                "filename": os.getcwd() + "/logs/application.log",
                "formatter": 'standard',
                "level": logging.WARNING
            },
        },
        'root': {
            'level': 'DEBUG',
            'handlers': ['console', 'file']
        }
    })
