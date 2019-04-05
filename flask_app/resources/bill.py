import os;
from flask import request, jsonify;
from flask_restful import Resource;
from flask_restful.reqparse import RequestParser;
from flask_app.errors import ValidationError;
from marshmallow import Schema, fields, post_load, validate, ValidationError as InvalidDataError;
from ..models import Bill;

class BillSchema(Schema):
	ID = fields.Int();
	# points to Bill.User.ID property when fetching from database
	User = fields.Int(required = True, attribute = "User.ID");
	PaymentAmount = fields.Float(required = True, validate = validate.Range(min=0.01, max=99999.99));
	PaymentDate = fields.Date(required = True);

	# TODO: check for unknown data fields in all other frameworks
	error_messages = {
		"unknown": "This field does not exist"
	}

	@post_load
	def fix_user_field(self, data):
		# we need to fix the User property due to Int field having attribute User.ID, which
		# causes it to return the data as a second level dict: {"User":{"ID": 1}}, which will
		# not work with the Insert query until fixed
		data["User"] = data["User"]["ID"];
		return data;

bill_schema = BillSchema();
bills_schema = BillSchema(many = True);

class BillResource(Resource):
	def get(self):
		bills = Bill.select();
		return bills_schema.dump([bill for bill in bills]);

	def post(self):
		# TODO: check that the month hasn't been used; do this for all frameworks
		try:
			request_data = request.get_json();
			validated_data = bill_schema.load(request_data);
		except InvalidDataError as error:
			raise ValidationError(metadata = error.messages);
		request_data["ID"] = Bill.insert(**validated_data).execute(database=None);
		return (request_data, 201);

	def put(self, id):
		try:
			request_data = request.get_json();
			valdiated_data = bill_schema.load(request_data);
		except InvalidDataError as error:
			raise ValidationError(metadata = error.messages);
		#Bill.update(**valdiated_data).where(Bill.ID == id).execute(database=None);
		return request_data;

	def delete(self, id):
		Bill.delete_by_id(id);
		return None, 204;