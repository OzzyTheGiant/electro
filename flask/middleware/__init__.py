import os
from datetime import datetime, timedelta
from flask import session
from flask_wtf.csrf import generate_csrf


class SessionMiddleware():
    def __call__(self):
        if "current_user" not in session or session["current_user"] is None:
            session["current_user"] = None
            session.permanent = True  # to enable persistent sessions and set expiration time


class CSRFMiddleware():
    def __call__(self, response):
        token = generate_csrf()
        response.set_cookie(
            key=os.getenv("XSRF_COOKIE"),
            value=token,
            max_age=os.getenv("SESSION_LIFETIME"),
            expires=datetime.now() + timedelta(seconds=int(os.getenv("SESSION_LIFETIME")) * 60),
            path="/",
            secure=os.getenv("APP_ENV") != "local",
            httponly=True
        )
