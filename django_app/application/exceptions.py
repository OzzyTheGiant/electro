"""App exception classes and custom exception handler"""
from django.http import JsonResponse;
from rest_framework.views import exception_handler;
from rest_framework.response import Response;
from rest_framework.exceptions import APIException, ValidationError;
from rest_framework.status import HTTP_404_NOT_FOUND;

def global_exception_handler(exception, context):
	"""Custom global exception handler that will output HTTP and Server errors"""
	response = exception_handler(exception, context);

	if response is None:
		response = Response();
		response.data = {};
		response.status_code = 500;

	if isinstance(exception, ValidationError):
		response.data["message"] = _craft_validation_error_message(exception);
	elif isinstance(exception, APIException):
		response.data["message"] = exception.detail;
	elif isinstance(exception.args[0], int):
		response.data["message"] = "Something went wrong while querying the database";
	else:
		response.data["message"] = exception.args[0];

	if "detail" in response.data:
		del response.data["detail"]  # duplicate of message, so it is discarded

	return response;

def _craft_validation_error_message(exception):
	"""iterate over validation error messages to output a sentence or paragraph"""
	message = "";
	for field_name, field_errors in exception.detail.items():
		message += field_name + ": ";
		for error_detail in field_errors:
			message += str(error_detail) + " ";
	return message;


def url_not_found_error_handler(request, exception):
	"""Return a json response on all Not Found urls"""
	return JsonResponse({"message":"This url could not be found"}, status = HTTP_404_NOT_FOUND);
