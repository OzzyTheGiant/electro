"""App views"""
from django.views.decorators.csrf import ensure_csrf_cookie, csrf_protect;
from django.utils.decorators import method_decorator;
from django.contrib.auth import authenticate, login, logout;
from rest_framework import viewsets, status;
from rest_framework.response import Response;
from rest_framework.decorators import api_view;
from rest_framework.views import APIView;
from rest_framework.exceptions import AuthenticationFailed;
from .serializers import UserSerializer, BillSerializer;
from .models import *;

@api_view(http_method_names = ["GET"])
@ensure_csrf_cookie
def home(request):
	"""API route for retrieving the main page of web application"""
	return Response(None, status = status.HTTP_204_NO_CONTENT);

@method_decorator(csrf_protect, 'dispatch')
@method_decorator(ensure_csrf_cookie, 'dispatch')
class LoginView(APIView):
	"""API endpoint that allows users to login"""
	def post(self, request, format = None):
		"""API login handler"""
		user = authenticate(username = request.data["username"], password = request.data['password']);
		if user is None:
			raise AuthenticationFailed;
		login(request, user);
		return Response(UserSerializer(user).data);

@method_decorator(ensure_csrf_cookie, 'dispatch')
class LogoutView(APIView):
	"""API endpoint that allows users to logout of application"""
	def post(self, request, format = None):
		logout(request);
		return Response(None, status = status.HTTP_204_NO_CONTENT);

@method_decorator(ensure_csrf_cookie, 'dispatch')
class BillViewSet(viewsets.ModelViewSet):
	"""API endpoint that allows bills to be viewed or edited."""
	queryset = Bill.objects.all();
	serializer_class = BillSerializer;
