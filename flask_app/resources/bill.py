import os;
from flask import request;
from flask_restful import Resource;
from flask_restful.reqparse import RequestParser;
from marshmallow import Schema, fields, post_load, validate, ValidationError
from ..models import Bill;

class BillSchema(Schema):
	ID = fields.Int(dump_only = True);
	# points to Bill.User.ID property when fetching from database
	User = fields.Int(required = True, attribute = "User.ID");
	# add custom validators to these fields
	PaymentAmount = fields.Float(required = True);
	PaymentDate = fields.Date(required = True);

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
		request_data = request.get_json();
		validated_data = bill_schema.load(request_data);
		query = Bill.insert(**validated_data);
		request_data["ID"] = query.execute(database=None);
		return request_data;