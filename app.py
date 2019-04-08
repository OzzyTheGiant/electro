import os;
import base64;
from flask import Flask, Blueprint, session, g;
from flask_kvsession import KVSessionExtension;
from simplekv.idgen import HashDecorator;
from simplekv.fs import WebFilesystemStore;
from dotenv import load_dotenv;
from flask_app import ElectroAPI;
from flask_app.resources import *;
from flask_app.middleware import *;
from flask_app.errors import *;

load_dotenv(); # get environment variables

app = Flask(__name__);
app.secret_key = base64.b64decode(os.getenv("APP_KEY").split(":")[1]);

# Configure Session using standard web session files,
# more store types available in simplekv package
# Hash Decorator provides session id
session_store = HashDecorator(WebFilesystemStore(
	os.getenv("FLASK_SESSION_FILE_PATH"), 
	os.getenv("FLASK_SESSION_URL_PREFIX")
));
KVSessionExtension(session_store, app);

# Create Api blueprint and add resources (routes)
api_blueprint = Blueprint('api', __name__);

# Create Api and with specified blueprint and add json resources
api = ElectroAPI(api_blueprint, catch_all_404s = True); # contains custom error handler
api.add_resource(BillResource, "/bills", "/bills/<int:id>", endpoint = "bill");
api.add_resource(UserResource, "/login", "/logout", endpoint = "login");

# Register blueprint to app
app.register_blueprint(api_blueprint, url_prefix="/api");

@app.before_request
def middleware_handler():
	manage_session();

@app.route("/api")
def home():
	return ("", 204);

if __name__ == "__main__":
	app.run();