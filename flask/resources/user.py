from flask import Blueprint, Response, request, session
from flask_jwt_extended import create_access_token, jwt_required, set_access_cookies
from flask_jwt_extended import unset_jwt_cookies
from flask_restx import Api, Resource
from werkzeug.exceptions import BadRequest, Unauthorized
from peewee import ProgrammingError, DoesNotExist
from argon2 import PasswordHasher
from marshmallow import ValidationError as InvalidDataError
from errors import DatabaseError
from models.user import User, UserSchema

blueprint = Blueprint("auth", __name__)
api = Api(blueprint, title = "Auth API", description = "App Authentication")


@api.route("/login", "/logout")
class UserResource(Resource):
    def post(self) -> Response:
        if request.path == '/api/login':
            return self.login()
        elif request.path == '/api/logout':
            return self.logout()


    def login(self) -> Response:
        try:
            user_schema = UserSchema()
            validated_data = user_schema.load(request.get_json())
            user = User.select().where(User.username == validated_data["username"]).get()
        except InvalidDataError as error:
            raise BadRequest(error.message)
        except DoesNotExist:
            raise Unauthorized("Username or password is incorrect")
        except ProgrammingError as error:
            raise DatabaseError(metadata={
                'sql_error_code': error.args[0],
                'sql_error_message': error.args[1]
            })

        ph = PasswordHasher()

        if not ph.verify(user.password, validated_data["password"]):
            raise Unauthorized("Username or password is not correct")
        else:
            current_user = user_schema.dump(user)
            token = create_access_token(current_user)
            response = Response(user.as_json())
            set_access_cookies(response, token)
            session["current_user"] = current_user

        return response


    @jwt_required()
    def logout(self) -> Response:
        response = Response(status = 204)
        unset_jwt_cookies(response)
        return response
