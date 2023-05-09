# from flask_wtf import CSRFProtect
from dotenv import load_dotenv
from flask import Response
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
# def middleware_pre_request_handler():
#     SessionMiddleware()()


# @app.after_request
# def middleware_post_request_handler(response):
#     CSRFMiddleware()(response)
#     return response


@app.route("/api")
def home():
    return Response("Hello world")


if __name__ == "__main__":
    app.run()
