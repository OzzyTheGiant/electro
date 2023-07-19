# from flask_wtf import CSRFProtect
import os
from dotenv import load_dotenv
# from flask import request
# from flask_jwt_extended import verify_jwt_in_request
from config.bootstrap import create_app
# from middleware import CSRFMiddleware, SessionMiddleware
# from middleware.csrf import CSRFProtectionExtension

load_dotenv("./.env")  # get environment variables

app = create_app()

# Add CSRFProtect extension to flask app
# NOTE: this extends flask_wtf.CSRFProtect class see class definition for details
# CSRFProtectionExtension(app)

# instantiate middleware classes and call them for procedures before request


# @app.before_request
# def verify_jwt_exists():
#     if request.path != "/api/login": verify_jwt_in_request()


# @app.after_request
# def middleware_post_request_handler(response):
#     CSRFMiddleware()(response)
#     return response


if __name__ == "__main__":
    app.run(port = os.environ["APP_PORT"])
