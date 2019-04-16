"""App exception classes and custom exception handler"""
from rest_framework.views import exception_handler;
from rest_framework.response import Response;
from rest_framework.exceptions import APIException;

def global_exception_handler(exception, context):
	"""Custom global exception handler that will output HTTP and Server errors"""
	response = exception_handler(exception, context);
	if response is not None:
		if isinstance(exception, APIException):
			response.data["message"] = exception.detail;
		else:
			response.data["message"] = exception.args[0];
		del response.data["detail"]; # duplicate of message, so it is discarded
	else:
		# for all other uncaught exceptions
		response = Response();
		response.data = {};
		response.status_code = 500
		if isinstance(exception.args[0], int): # since we can't provide custom Database error class, check for sql error code
			response.data["message"] = "Something went wrong while querying the database";
		else:
			response.data["message"] = exception.args[0];
	return response;
