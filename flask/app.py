from flask import Flask, Blueprint
# from flask_wtf import CSRFProtect
from dotenv import load_dotenv
from . import ElectroAPI, config
from config import configure_logging
from resources import BillResource, UserResource
from middleware import CSRFMiddleware, SessionMiddleware
# from middleware.csrf import CSRFProtectionExtension

load_dotenv()  # get environment variables
configure_logging()  # configure loggers before starting app

app = Flask(__name__)
app.config.update(**config)

# Add CSRFProtect extension to flask app
# NOTE: this extends flask_wtf.CSRFProtect class see class definition for details
# CSRFProtectionExtension(app)

# Create Api blueprint and add resources (routes)
api_blueprint = Blueprint('api', __name__)
# contains custom error handler
api = ElectroAPI(api_blueprint, catch_all_404s=True)
api.add_resource(BillResource, "/bills", "/bills/<int:id>", endpoint="bill")
api.add_resource(UserResource, "/login", "/logout", endpoint="login")

# Register blueprint to app
app.register_blueprint(api_blueprint, url_prefix="/api")

# instantiate middleware classes and call them for procedures before request


@app.before_request
def middleware_pre_request_handler():
    SessionMiddleware()()


@app.after_request
def middleware_post_request_handler(response):
    CSRFMiddleware()(response)
    return response


@app.route("/api")
def home():
    return ("", 204)


if __name__ == "__main__":
    app.run()
