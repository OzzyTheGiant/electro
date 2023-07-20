# from flask import request
from typing import List, Tuple, Union
from flask import Blueprint, request
from flask_jwt_extended import jwt_required
from flask_restx import Resource, Api
from werkzeug.exceptions import HTTPException, BadRequest, NotFound
from peewee import ProgrammingError
from marshmallow import ValidationError
from models.bill import Bill, BillSchema
from errors import error_handler
from errors.exceptions import DatabaseError

blueprint = Blueprint("bills", __name__, url_prefix = "/bills")
api = Api(blueprint, title = "Bills API Endpoints", description = "Electric Bills")
ns = api.namespace("bills", description = "Bills API routes")


@api.errorhandler(Exception)
def api_error_handler(error: Exception):
    if isinstance(error, HTTPException): return error_handler(error)
    raise error


@ns.route("", "/<int:id>")
class BillResource(Resource):
    @jwt_required()
    def get(self) -> List[Bill]:
        try:
            bills = Bill.select()
            return [bill.as_dict() for bill in bills]
        except ProgrammingError as error:
            print(error.args)
            raise DatabaseError(metadata = {
                'sql_error_code': error.args[0],
                'sql_error_message': error.args[1]
            })


    @jwt_required()
    def post(self) -> Tuple[dict, int]:
        try:
            bill_schema = BillSchema()
            request_data = request.get_json()
            validated_data = bill_schema.load(request_data)
            request_data["id"] = Bill.insert(**validated_data).execute()
        except ValidationError as error:
            raise BadRequest(error.normalized_messages())
        except ProgrammingError as error:
            raise DatabaseError(metadata = {
                'sql_error_code': error.args[0],
                'sql_error_message': error.args[1]
            })

        return (request_data, 201)


    @jwt_required()
    def put(self, id: Union[int, None] = None) -> dict:
        try:
            bill_schema = BillSchema()
            request_data = request.get_json()
            validated_data = bill_schema.load(request_data)
            rows = Bill.update(**validated_data).where(Bill.id == request_data["id"]).execute()
        except ValidationError as error:
            raise BadRequest(error.normalized_messages())
        except ProgrammingError as error:
            raise DatabaseError(metadata = {
                'sql_error_code': error.args[0],
                'sql_error_message': error.args[1]
            })

        if not rows: raise DatabaseError()

        return request_data


    @jwt_required()
    def delete(self, id) -> Tuple[None, int]:
        try:
            rows = Bill.delete_by_id(id)
        except ProgrammingError as error:
            raise DatabaseError(metadata = {
                'sql_error_code': error.args[0],
                'sql_error_message': error.args[1]
            })

        if not rows: raise NotFound("This bill was not found")
        return None, 204
