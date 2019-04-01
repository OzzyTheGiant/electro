from flask_restful import Resource, fields, marshal;
from flask_restful.reqparse import RequestParser;
from ..models import Bill;

class BillResource(Resource):
	data_fields = {
		"ID":fields.Integer,
		"User":fields.Integer,
		"PaymentAmount":fields.Float,
		"PaymentDate":fields.String
	}

	def get(self):
		bills = Bill.select().dicts();
		return [marshal(bill, self.data_fields) for bill in bills];