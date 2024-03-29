"""
Django settings for electro project.

Generated by 'django-admin startproject' using Django 4.2.5.

For more information on this file, see
https://docs.djangoproject.com/en/4.2/topics/settings/

For the full list of settings and their values, see
https://docs.djangoproject.com/en/4.2/ref/settings/
"""

import os, sys, logging
from datetime import timedelta
from pathlib import Path

environment = os.environ["APP_ENV"]

# Build paths inside the project like this: BASE_DIR / 'subdir'.
BASE_DIR = Path(__file__).resolve().parent.parent


# Quick-start development settings - unsuitable for production
# See https://docs.djangoproject.com/en/4.2/howto/deployment/checklist/

# SECURITY WARNING: keep the secret key used in production secret!
SECRET_KEY = os.environ["APP_KEY"]

# SECURITY WARNING: don't run with debug turned on in production!
DEBUG = environment == "development"

ALLOWED_HOSTS = ["electro.test", "localhost"]


# Application definition

INSTALLED_APPS = [
    "django.contrib.admin",
    "django.contrib.auth",
    "django.contrib.contenttypes",
    "django.contrib.sessions",
    "django.contrib.messages",
    "django.contrib.staticfiles",
    "rest_framework",
    "electro",
]

MIDDLEWARE = [
    "django.middleware.security.SecurityMiddleware",
    "django.contrib.sessions.middleware.SessionMiddleware",
    "django.middleware.common.CommonMiddleware",
    "django.middleware.csrf.CsrfViewMiddleware",
    "django.contrib.auth.middleware.AuthenticationMiddleware",
    "django.contrib.messages.middleware.MessageMiddleware",
    "django.middleware.clickjacking.XFrameOptionsMiddleware",
]

ROOT_URLCONF = "electro.urls"

TEMPLATES = [
    {
        "BACKEND": "django.template.backends.django.DjangoTemplates",
        "DIRS": [],
        "APP_DIRS": True,
        "OPTIONS": {
            "context_processors": [
                "django.template.context_processors.debug",
                "django.template.context_processors.request",
                "django.contrib.auth.context_processors.auth",
                "django.contrib.messages.context_processors.messages",
            ],
        },
    },
]

WSGI_APPLICATION = "electro.wsgi.application"


# Database
# https://docs.djangoproject.com/en/4.2/ref/settings/#databases

DATABASES = {
    "default": {
        "ENGINE": f"django.db.backends.{os.environ['DB_CONNECTION']}",
        "NAME": os.environ["DB_DATABASE"],
        "HOST": os.environ["DB_HOST"],
        "PORT": os.environ["DB_PORT"],
        "USER": os.environ["DB_USER"],
        "PASSWORD": os.environ["DB_PASSWORD"],
    }
}


# Password validation
# https://docs.djangoproject.com/en/4.2/ref/settings/#auth-password-validators

AUTH_PASSWORD_VALIDATORS = [
    {
        "NAME": "django.contrib.auth.password_validation.UserAttributeSimilarityValidator",
    },
    {
        "NAME": "django.contrib.auth.password_validation.MinimumLengthValidator",
    },
    {
        "NAME": "django.contrib.auth.password_validation.CommonPasswordValidator",
    },
    {
        "NAME": "django.contrib.auth.password_validation.NumericPasswordValidator",
    },
]

# Password Hashers
PASSWORD_HASHERS = [
    "django.contrib.auth.hashers.Argon2PasswordHasher",
    "django.contrib.auth.hashers.BCryptSHA256PasswordHasher"
]

AUTH_USER_MODEL = "electro.User"
AUTHENTICATION_BACKENDS = ["electro.authentication.APIBackend"]

# JWT

SIMPLE_JWT = {
    "ACCESS_TOKEN_LIFETIME": timedelta(minutes = int(os.environ["JWT_ACCESS_TOKEN_EXPIRES"]) * 60),
    "REFRESH_TOKEN_LIFETIME": timedelta(minutes = int(os.environ["JWT_ACCESS_TOKEN_EXPIRES"]) * 60)
}

# Sessions

SESSION_ENGINE = "django.contrib.sessions.backends.signed_cookies"
SESSION_COOKIE_AGE = int(os.environ["JWT_ACCESS_TOKEN_EXPIRES"]) * 60
SESSION_COOKIE_HTTPONLY = True
SESSION_COOKIE_NAME = os.getenv("SESSION_NAME")
SESSION_COOKIE_SECURE = environment != "development"


# CSRF
# Note: When using CSRF sessions, use ensure_csrf_cookie since the only other way to
# get token is by rendering it into an html template
CSRF_USE_SESSIONS = False
# the following setting is for the csrf token cookie when CSRF_USE_SESSIONS = False,
# however, if using sessions, use your own custom middleware to set the cookie, as
# ensure_csrf_cookie only puts out session cookie,
# plus this will be necessary for validating logins with tokens
CSRF_COOKIE_NAME = os.environ["JWT_CSRF_COOKIE_NAME"]
# Make sure the token header is written like this here in the settings, otherwise,
# django won't find the token
CSRF_HEADER_NAME = "HTTP_X_CSRF_TOKEN"
CSRF_COOKIE_SECURE = environment != "local"
CSRF_COOKIE_AGE = int(os.environ["JWT_ACCESS_TOKEN_EXPIRES"]) * 60
CSRF_TOKEN_HTTPONLY = False
CSRF_FAILURE_VIEW = "electro.views.csrf_failure"


# Internationalization
# https://docs.djangoproject.com/en/4.2/topics/i18n/

LANGUAGE_CODE = "en-us"

TIME_ZONE = "UTC"

USE_I18N = True

USE_TZ = True


# Static files (CSS, JavaScript, Images)
# https://docs.djangoproject.com/en/4.2/howto/static-files/

STATIC_URL = "static/"
APPEND_SLASH = False


# Default primary key field type
# https://docs.djangoproject.com/en/4.2/ref/settings/#default-auto-field

DEFAULT_AUTO_FIELD = "django.db.models.BigAutoField"

# REST Framework

REST_FRAMEWORK = {
    "EXCEPTION_HANDLER": "electro.exceptions.global_exception_handler",
    "DEFAULT_AUTHENTICATION_CLASSES": [
        "electro.authentication.CookieBasedJWTAuthentication"
    ]
}

# Logging

LOGGING = {
    "version": 1,
    "disable_existing_logger": False,
    "formatters": {
        "standard": {
            "class": "logging.Formatter",
            "format": "[%(asctime)s] %(levelname)s - %(module)s: %(message)s; Metadata:%(args)s",
        },
        "colored": {
            "class": "colorlog.ColoredFormatter",
            "format": "[%(asctime)s] %(log_color)s%(levelname)s%(reset)s - %(module)s: %(message)s; Metadata: %(args)s"  # noqa: E501
        },
    },
    "handlers": {
        "console": {
            "class": "logging.StreamHandler"
            if environment != "local"
            else "colorlog.StreamHandler",
            "stream": sys.stdout,
            "formatter": "standard" if environment != "local" else "colored",
            "level": logging.DEBUG,
        },
        "file": {
            "class": "logging.FileHandler",
            "filename": os.getcwd() + "/logs/app.log",
            "formatter": "standard",
            "level": logging.WARNING,
        },
    },
    "loggers": {
        "django": {
            "handlers": ["console", "file"],
            "level": "INFO",
            "propagate": False,  # prevent duplicate log records
        },
        "electro.exceptions": {
            "handlers": ["console", "file"],
            "level": "INFO",
            "propagate": False,
        },
    },
    "root": {"level": "DEBUG", "handlers": ["console", "file"]},
}
