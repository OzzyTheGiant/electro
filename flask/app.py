import os;
from flask import Flask, Blueprint, session, jsonify;
from flask_wtf import CSRFProtect;
from flask_kvsession import KVSessionExtension;
from simplekv.idgen import HashDecorator;
from simplekv.fs import WebFilesystemStore;
from dotenv import load_dotenv;
from python_flask import ElectroAPI, config;
from python_flask.config import configure_logging;
from python_flask.resources import *;
from python_flask.middleware import *;
from python_flask.middleware.csrf import CSRFProtectionExtension;

load_dotenv(); # get environment variables
configure_logging(); # configure loggers before starting app

app = Flask(__name__);
app.config.update(**config);

# Configure Session using standard web session files,
# more store types available in simplekv package
# Hash Decorator provides session id
session_store = HashDecorator(WebFilesystemStore(
	os.getenv("FLASK_SESSION_FILE_PATH"), 
	os.getenv("FLASK_SESSION_URL_PREFIX")
));
KVSessionExtension(session_store, app);

# Add CSRFProtect extension to flask app
# NOTE: this extends flask_wtf.CSRFProtect class; see class definition for details
CSRFProtectionExtension(app);

# Create Api blueprint and add resources (routes)
api_blueprint = Blueprint('api', __name__);
api = ElectroAPI(api_blueprint, catch_all_404s = True); # contains custom error handler
api.add_resource(BillResource, "/bills", "/bills/<int:id>", endpoint = "bill");
api.add_resource(UserResource, "/login", "/logout", endpoint = "login");

# Register blueprint to app
app.register_blueprint(api_blueprint, url_prefix="/api");

# instantiate middleware classes and call them for procedures before request
@app.before_request
def middleware_pre_request_handler():
	SessionMiddleware()();

@app.after_request
def middleware_post_request_handler(response):
	CSRFMiddleware()(response);
	return response;

@app.route("/api")
def home():
	return ("", 204);

if __name__ == "__main__":
	app.run();