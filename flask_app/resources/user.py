import os;
from flask import request;
from flask_restful import Resource;
from werkzeug.exceptions import BadRequest;
from peewee import ProgrammingError, DoesNotExist;
from bcrypt import checkpw;
from marshmallow import Schema, fields, validate, ValidationError as InvalidDataError;
from flask_app.errors import *;
from ..models import User;

class UserSchema(Schema):
	ID = fields.Int();
	Username = fields.String(required = True);
	Password = fields.String(required = True, load_only = True);

user_schema = UserSchema();

class UserResource(Resource):
	def post(self):
		# TODO: check that the month hasn't been used; do this for all frameworks
		if request.path == '/api/login':
			return UserResource.login(self);
		elif request.path == '/api/logout':
			return UserResource.logout(self);

	def login(self):
		try:
			request_data = request.get_json();
			validated_data = user_schema.load({
				"Username":request_data["username"],
				"Password":request_data["password"]
			});
			user = User.select().where(User.Username == validated_data["Username"]).get();
		except InvalidDataError as error:
			raise ValidationError(metadata = error.messages);
		except BadRequest as error:
			raise EmptyRequestBodyError();
		except DoesNotExist as error:
			raise AuthenticationError();
		except ProgrammingError as error:
			raise DatabaseError(metadata = {
				'sql_error_code':error.args[0],
				'sql_error_message':error.args[1]
			});
		if not checkpw(validated_data["Password"].encode("utf-8"), user.Password.encode("utf-8")):
			raise AuthenticationError();
		return user_schema.dump(user);

	def logout(self):
		pass;