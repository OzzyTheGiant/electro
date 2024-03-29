import os
from typing import Union
from datetime import timedelta
from flask import Flask
from models import database
from resources.user import blueprint as auth_bp
from resources.bill import blueprint as bills_bp
from services.logger import create_logger
from services.auth import jwt
from errors import error_handler


class AppConfig(object):
    def __init__(self) -> None:
        environment = os.environ["APP_ENV"]
        self.ENV = "development" if environment == "local" else environment
        self.SECRET_KEY = os.environ["APP_KEY"]
        self.JWT_SECRET_KEY = os.environ["JWT_SECRET_KEY"]
        self.JWT_TOKEN_LOCATION = ["headers", "cookies"]
        self.JWT_COOKIE_SECURE = False  # set to False since Flask is running in a container
        self.JWT_COOKIE_CSRF_PROTECT = True
        self.JWT_CSRF_PROTECTION = ["GET", "POST", "PUT", "PATCH", "DELETE"]
        self.JWT_ACCESS_COOKIE_NAME = os.getenv("JWT_ACCESS_COOKIE_NAME", "electro")
        self.JWT_ACCESS_CSRF_COOKIE_NAME = os.getenv("JWT_CSRF_COOKIE_NAME", "electro-x")
        self.JWT_ACCESS_TOKEN_EXPIRES = timedelta(
            hours = int(os.getenv("JWT_ACCESS_TOKEN_EXPIRES", 1))
        )
        # multiply minutes times seconds
        self.MAX_CONTENT_LENGTH = 1048576  # 1 MB
        self.DB_DATABASE = os.environ["DB_DATABASE"] or "Electro"
        self.DB_HOST = os.environ["DB_HOST"]
        self.DB_PORT = int(os.environ["DB_PORT"]) or 3306
        self.DB_USER = os.environ["DB_USER"]
        self.DB_PASSWORD = os.environ["DB_PASSWORD"]


def create_app(config: Union[AppConfig, None] = None) -> Flask:
    app = Flask(__name__)

    app.config.from_object(config if config else AppConfig())
    create_logger()

    # Register blueprints to app
    app.register_blueprint(bills_bp, url_prefix ="/api")
    app.register_blueprint(auth_bp, url_prefix = "/api")
    app.register_error_handler(Exception, error_handler)

    jwt.init_app(app)
    database.init(
        app.config["DB_DATABASE"],
        host = app.config["DB_HOST"],
        user = app.config["DB_USER"],
        password = app.config["DB_PASSWORD"]
    )

    return app
