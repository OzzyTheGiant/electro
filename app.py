from flask import Flask, Blueprint, jsonify;
from werkzeug.exceptions import NotFound;
from dotenv import load_dotenv;
from flask_app import ElectroAPI;
from flask_app.resources import BillResource;
from flask_app.errors import *;

load_dotenv(); # get environment variables

app = Flask(__name__);

# Create Api blueprint and add resources (routes)
api_blueprint = Blueprint('api', __name__);
api = ElectroAPI(api_blueprint); # contains custom error handler
api.add_resource(BillResource, "/bills", "/bills/<int:id>", endpoint = "bill");

# Regsiter blueprint to app
app.register_blueprint(api_blueprint, url_prefix="/api");

@app.route("/")
def home():
	return "Hello world";

if __name__ == "__main__":
	app.run();