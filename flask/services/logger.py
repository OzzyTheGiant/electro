import os, sys, logging
from logging.config import dictConfig


def create_logger():
    if not os.path.exists(os.environ["LOG_FILE"]):
        file = open(os.environ["LOG_FILE"], "x")
        file.close()

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
                    if os.environ["APP_ENV"] != 'local'
                    else "colorlog.StreamHandler"
                ),
                "stream": sys.stdout,
                "formatter": "standard" if os.environ["APP_ENV"] != 'local' else 'colored',
                "level": logging.DEBUG
            },
            'file': {
                "class": "logging.FileHandler",
                "filename": os.environ["LOG_FILE"],
                "formatter": 'standard',
                "level": logging.WARNING
            },
        },
        'root': {
            'level': 'DEBUG',
            'handlers': ['console', 'file']
        }
    })
