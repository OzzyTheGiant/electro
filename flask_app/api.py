from flask import jsonify;
from flask_restful import Api, abort;

class ElectroAPI(Api):
	def handle_error(self, error):
		"""Global error handler for all API routes"""
		return (jsonify({"message":error.description}), error.code);