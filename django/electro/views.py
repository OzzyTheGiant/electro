"""App views"""
import os
from typing import Any, cast
from datetime import datetime, timedelta
from django.views.decorators.csrf import ensure_csrf_cookie  # csrf_protect
# from django.utils.decorators import method_decorator
from django.contrib.auth import authenticate
from rest_framework import status
from rest_framework.viewsets import ModelViewSet
from rest_framework.response import Response
from rest_framework.decorators import api_view
from rest_framework.views import APIView
from rest_framework.exceptions import AuthenticationFailed
from rest_framework_simplejwt.tokens import RefreshToken
from electro.authentication import JWTAuthenticationMixin
from electro.models import Bill
from electro.serializers import UserSerializer, BillSerializer


@api_view(http_method_names = ["GET"])
@ensure_csrf_cookie
def home():
    """API route for retrieving the main page of web application"""
    return Response(None, status = status.HTTP_204_NO_CONTENT)


# @method_decorator(csrf_protect, "dispatch")
# @method_decorator(ensure_csrf_cookie, "dispatch")
class LoginView(APIView):
    """API endpoint that allows users to login"""

    def post(self, request, format = None):
        """API login handler"""

        user = authenticate(
            username = request.data["username"],
            password = request.data["password"]
        )

        if user is None: raise AuthenticationFailed

        refresh_token = cast(Any, RefreshToken.for_user(user))
        token = str(refresh_token.access_token)
        response = Response(UserSerializer(user).data)

        response.set_cookie(
            os.environ["JWT_ACCESS_COOKIE_NAME"],
            token,
            httponly = True,
            expires = datetime.now() + timedelta(
                minutes = int(os.environ["JWT_ACCESS_TOKEN_EXPIRES"]) * 60
            )
        )

        return response


# @method_decorator(ensure_csrf_cookie, "dispatch")
class LogoutView(JWTAuthenticationMixin, APIView):
    """API endpoint that allows users to logout of application"""

    def post(self, request, format = None):
        response = Response(None, status = status.HTTP_204_NO_CONTENT)
        response.delete_cookie(os.environ["JWT_ACCESS_COOKIE_NAME"])
        return response


# @method_decorator(ensure_csrf_cookie, "dispatch")
class BillViewSet(JWTAuthenticationMixin, ModelViewSet):
    """API endpoint that allows bills to be viewed or edited."""

    queryset = Bill.objects.all()
    serializer_class = BillSerializer
