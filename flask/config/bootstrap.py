import os
from typing import Union
from flask import Flask, current_app, jsonify
from services.logger import create_logger
from models import database
from resources.bill import blueprint as bills_bp


class AppConfig(object):
    def __init__(self) -> None:
        environment = os.environ["APP_ENV"]
        self.ENV = "development" if environment == "local" else environment
        self.SECRET_KEY = os.environ["APP_KEY"]
        # multiply minutes times seconds
        self.MAX_CONTENT_LENGTH = 1048576  # 1 MB
        self.SESSION_REFRESH_EACH_REQUEST = True
        self.WTF_CSRF_HEADERS = ['X-XSRF-TOKEN', 'X-CSRF-TOKEN']
        self.DB_DATABASE = os.environ["DB_DATABASE"] or "Electro"
        self.DB_HOST = os.environ["DB_HOST"]
        self.DB_PORT = int(os.environ["DB_PORT"]) or 3306
        self.DB_USER = os.environ["DB_USER"]
        self.DB_PASSWORD = os.environ["DB_PASSWORD"]


def error_handler(error: Exception):
    """Global error handler for all API routes"""
    print("was here")
    if hasattr(error, "loggable") and error.loggable:
        if hasattr(error, 'description'):  # check if it's a custom error with description text
            if hasattr(error, 'metadata'):  # check if custom error class allows metadata
                current_app.logger.error(error.description, error.metadata)
            else:
                current_app.logger.error(error.description)
        else:  # possible uncaught or unexpected exceptions
            current_app.logger.error(error.args[0])  # message

    return (jsonify({ "message": error.description or error.args[0] }), error.code)


def create_app(config: Union[AppConfig, None] = None) -> Flask:
    app = Flask(__name__)

    app.config.from_object(config if config else AppConfig())
    create_logger()

    # Register blueprints to app
    app.register_blueprint(bills_bp, url_prefix="/api")

    app.register_error_handler(Exception, error_handler)

    database.init(
        app.config["DB_DATABASE"],
        host = app.config["DB_HOST"],
        user = app.config["DB_USER"],
        password = app.config["DB_PASSWORD"]
    )

    return app
