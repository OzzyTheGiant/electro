"""Custom authentication backend"""
from django_app.application.models import User;
from bcrypt import checkpw;

class APIBackend:
	"""custom API authentication backend class that uses Bcrypt directly"""
	def authenticate(self, request, username = None, password = None):
		try:
			user = User.objects.get(Username__exact = username);
		except User.DoesNotExist:
			return None;
		if not checkpw(password.encode('utf-8'), user.Password.encode('utf-8')):
			return None;
		return user;

	def get_user(self, user_id):
		try:
			return User.objects.get(pk=user_id)
		except User.DoesNotExist:
			return None;
