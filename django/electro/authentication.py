"""Custom authentication backend"""
import os
from typing import Optional, Tuple, Union
from django.contrib.auth.hashers import check_password
from rest_framework.permissions import IsAuthenticated
from rest_framework.request import Request
from rest_framework_simplejwt.authentication import JWTStatelessUserAuthentication, AuthUser
from rest_framework_simplejwt.tokens import Token
from electro.models import User


class APIBackend:
    """custom API authentication backend class that uses Bcrypt directly"""

    def authenticate(self, request, username = None, password: Union[str, None] = None):
        try:
            user = User.objects.get(username__exact = username)
        except User.DoesNotExist:
            return None
        if password and check_password(password, user.password):
            return None
        return user


    def get_user(self, user_id):
        try:
            return User.objects.get(pk = user_id)
        except User.DoesNotExist:
            return None


class CookieBasedJWTAuthentication(JWTStatelessUserAuthentication):
    def authenticate(self, request: Request) -> Optional[Tuple[AuthUser, Token]]:
        cookie_name = os.environ["JWT_ACCESS_COOKIE_NAME"]
        raw_token = request.COOKIES[cookie_name] if cookie_name in request.COOKIES else None

        if raw_token is None: return None
        validated_token = self.get_validated_token(raw_token)

        return self.get_user(validated_token), validated_token


class JWTAuthenticationMixin():
    """Defines the authentication and permission properties for views and viewsets"""
    authentication_classes = [CookieBasedJWTAuthentication]
    permission_classes = [IsAuthenticated]
