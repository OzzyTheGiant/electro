from flask import Flask, Blueprint, jsonify;
from flask_restful import Api;
from dotenv import load_dotenv;
from flask_app.resources import BillResource;

load_dotenv(); # get environment variables

app = Flask(__name__);

# Create Api blueprint and add resources (routes)
api_blueprint = Blueprint('api', __name__);
api = Api(api_blueprint);
api.add_resource(BillResource, "/bills");

# Regsiter blueprint to app
app.register_blueprint(api_blueprint, url_prefix="/api");

@app.route("/")
def home():
	return "Hello world";

# def error_handler(error):
# 	return (jsonify({"message":error.message}), error.code);

# app.register_error_handler()

if __name__ == "__main__":
	app.run();