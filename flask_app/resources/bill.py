import os;
from flask import request;
from flask_restful import Resource;
from werkzeug.exceptions import BadRequest;
from peewee import ProgrammingError;
from marshmallow import Schema, fields, post_load, validate, ValidationError as InvalidDataError;
from flask_app.errors import ValidationError, NotFoundError, EmptyRequestBodyError, DatabaseError;
from ..models import Bill;

# TODO: check for unknown data fields in all other frameworks
class BillSchema(Schema):
	ID = fields.Int();
	# points to Bill.User.ID property when fetching from database
	User = fields.Int(required = True, attribute = "User.ID");
	PaymentAmount = fields.Float(required = True, validate = validate.Range(min=0.01, max=99999.99));
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
		try:
			bills = Bill.select();
			return bills_schema.dump([bill for bill in bills])
		except ProgrammingError as error:
			raise DatabaseError(metadata = {
				'sql_error_code':error.args[0],
				'sql_error_message':error.args[1]
			});

	def post(self):
		# TODO: check that the month hasn't been used; do this for all frameworks
		try:
			request_data = request.get_json();
			validated_data = bill_schema.load(request_data);
			request_data["ID"] = Bill.insert(**validated_data).execute(database=None);
		except InvalidDataError as error:
			raise ValidationError(metadata = error.messages);
		except BadRequest as error:
			raise EmptyRequestBodyError();
		except ProgrammingError as error:
			raise DatabaseError(metadata = {
				'sql_error_code':error.args[0],
				'sql_error_message':error.args[1]
			});
		return (request_data, 201);

	def put(self, id):
		try:
			request_data = request.get_json();
			valdiated_data = bill_schema.load(request_data);
			rows = Bill.update(**valdiated_data).where(Bill.ID == id).execute(database=None);
		except InvalidDataError as error:
			raise ValidationError(metadata = error.messages);
		except BadRequest as error:
			raise EmptyRequestBodyError
		except ProgrammingError as error:
			raise DatabaseError(metadata = {
				'sql_error_code':error.args[0],
				'sql_error_message':error.args[1]
			});
		if not rows:
			raise NotFoundError(item = "bill");
		return request_data;

	def delete(self, id):
		try:
			rows = Bill.delete_by_id(id);
		except ProgrammingError as error:
			raise DatabaseError(metadata = {
				'sql_error_code':error.args[0],
				'sql_error_message':error.args[1]
			});
		if not rows:
			raise NotFoundError(item = "bill");
		return None, 204;