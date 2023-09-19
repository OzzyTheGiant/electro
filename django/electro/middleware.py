"""Custom CSRF Middleware for generating CSRF cookies"""
from django.conf import settings
from django.middleware.csrf import get_token


class CSRFCookieMiddleware:
    """Sets CSRF cookie no matter what. This is due to Django not providing cookie if CSRF is
    used with sessions"""

    def __init__(self, get_response):
        self.get_response = get_response

    def __call__(self, request):
        response = self.get_response(request)

        if settings.CSRF_USE_SESSIONS:
            response.set_cookie(
                settings.CSRF_COOKIE_NAME,
                get_token(request),
                max_age = settings.CSRF_COOKIE_AGE,
                domain = settings.CSRF_COOKIE_DOMAIN,
                path = settings.CSRF_COOKIE_PATH,
                secure = settings.CSRF_COOKIE_SECURE,
                httponly = settings.CSRF_COOKIE_HTTPONLY,
                samesite = settings.CSRF_COOKIE_SAMESITE,
            )

        return response
