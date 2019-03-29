from flask_restful import Resource;

class Bill(Resource):
	def get(self):
		return {"message": "Hello world"};