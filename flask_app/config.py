import os;
import base64;
from datetime import timedelta;

config = {
	"ENV": "development" if os.getenv("APP_ENV") == "local" else os.getenv("APP_ENV"),
	"SECRET_KEY": base64.b64decode(os.getenv("APP_KEY").split(":")[1]),
	"SESSION_COOKIE_NAME": os.getenv("SESSION_COOKIE"),
	"SESSION_COOKIE_SECURE": os.getenv("APP_ENV") != "local",
	"PERMANENT_SESSION_LIFETIME": timedelta(seconds = int(os.getenv("SESSION_LIFETIME")) * 60), # multiply minutes times seconds
	"MAX_CONTENT_LENGTH": 1048576, # 1 MB
	"SESSION_REFRESH_EACH_REQUEST": True,
	"WTF_CSRF_HEADERS":['X-XSRF-TOKEN', 'X-CSRF-TOKEN']
};