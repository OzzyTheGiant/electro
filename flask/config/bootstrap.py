import os
from typing import Union
from flask import Blueprint, Flask
from dotenv import load_dotenv
from services.logger import create_logger

load_dotenv()  # get environment variables

environment = os.environ["APP_ENV"]


class AppConfig(object):
    def __init__(self) -> None:
        self.ENV = "development" if environment == "local" else environment,
        self.SECRET_KEY = os.environ["APP_KEY"]
        # multiply minutes times seconds
        self.MAX_CONTENT_LENGTH = 1048576  # 1 MB
        self.SESSION_REFRESH_EACH_REQUEST = True
        self.WTF_CSRF_HEADERS = ['X-XSRF-TOKEN', 'X-CSRF-TOKEN']


def create_app(config: Union[AppConfig, None] = None) -> Flask:
    app = Flask(__name__)

    app.config.from_object(config if config else AppConfig())
    create_logger()

    # Create Api blueprint and add resources (routes)
    api_blueprint = Blueprint('api', __name__)
    # contains custom error handler
    # api = ElectroAPI(api_blueprint, catch_all_404s=True)
    # api.add_resource(BillResource, "/bills", "/bills/<int:id>", endpoint="bill")
    # api.add_resource(UserResource, "/login", "/logout", endpoint="login")

    # Register blueprint to app
    app.register_blueprint(api_blueprint, url_prefix="/api")

    return app
