"""Custom authentication backend"""
from typing import Union
from django.contrib.auth.hashers import check_password
from electro.models import User


class APIBackend:
    """custom API authentication backend class that uses Bcrypt directly"""

    def authenticate(self, request, username = None, password: Union[str, None] = None):
        try:
            user = User.objects.get(Username__exact = username)
        except User.DoesNotExist:
            return None
        if password and not check_password(password, user.password):
            return None
        return user

    def get_user(self, user_id):
        try:
            return User.objects.get(pk = user_id)
        except User.DoesNotExist:
            return None
