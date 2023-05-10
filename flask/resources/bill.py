# from flask import request
from typing import Union
from flask import Blueprint, request
from flask_restx import Resource, Api
from werkzeug.exceptions import BadRequest, NotFound
from peewee import ProgrammingError
from marshmallow import ValidationError as InvalidDataError
from models.bill import Bill, BillSchema
from errors.exceptions import DatabaseError

blueprint = Blueprint("bills", __name__, url_prefix = "/bills")
api = Api(blueprint, title = "Bills API Endpoints", description = "Electric Bills")
ns = api.namespace("bills", description = "Bills API routes")


@ns.route("/", "/<int:id>")
class BillResource(Resource):
    def get(self):
        try:
            bills = Bill.select()
            return [bill.as_dict() for bill in bills]
        except ProgrammingError as error:
            print(error.args)
            raise DatabaseError(metadata = {
                'sql_error_code': error.args[0],
                'sql_error_message': error.args[1]
            })


    def post(self):
        try:
            bill_schema = BillSchema()
            request_data = request.get_json()
            validated_data = bill_schema.load(request_data)
            request_data["id"] = Bill.insert(**validated_data).execute()
        except InvalidDataError as error: raise BadRequest(error.message)
        except ProgrammingError as error:
            raise DatabaseError(metadata = {
                'sql_error_code': error.args[0],
                'sql_error_message': error.args[1]
            })

        return (request_data, 201)


    def put(self, id: Union[int, None] = None):
        try:
            bill_schema = BillSchema()
            request_data = request.get_json()
            validated_data = bill_schema.load(request_data)
            rows = Bill.update(**validated_data).where(Bill.id == request_data["id"]).execute()
        except InvalidDataError as error:
            raise BadRequest(error.message)
        except ProgrammingError as error:
            raise DatabaseError(metadata = {
                'sql_error_code': error.args[0],
                'sql_error_message': error.args[1]
            })

        if not rows: raise DatabaseError()

        return request_data


    def delete(self, id):
        try:
            rows = Bill.delete_by_id(id)
        except ProgrammingError as error:
            raise DatabaseError(metadata = {
                'sql_error_code': error.args[0],
                'sql_error_message': error.args[1]
            })

        if not rows: raise NotFound("This bill was not found")
        return None, 204
