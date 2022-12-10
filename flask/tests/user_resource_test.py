from unittest import TestCase, main;
from unittest.mock import Mock, MagicMock, patch;
from peewee import *;
from werkzeug.exceptions import BadRequest;
from ..resources import UserResource;
from ..models import User, Bill;
from ..errors import ValidationError, EmptyRequestBodyError, AuthenticationError;

MODELS = (Bill, User);
test_db = SqliteDatabase(":memory:");

class UserResourceTest(TestCase):
	# data to be used in test database
	user = {"ID": 1, "Username":"OzzyTheGiant", "Password":"$2a$10$Cj66BNdUZhkMvStI5jfQoetgzSvkaQIwJuIRDPIa1zgFsFPXkbqr2"}

	# request body
	json = {"username":"OzzyTheGiant", "password":"notarealpassword"};

	# mocked request object and session
	request = Mock();
	session = MagicMock();

	user_resource = UserResource();

	def setUp(self):
		test_db.bind(MODELS, bind_refs = False, bind_backrefs = False);
		test_db.connect()
		test_db.create_tables(MODELS, safe = True);
		User.insert(self.user).execute(database = None);
		self.session.regenerate = Mock();

	def test_login_validatesCredentialsAndStartsSession(self):
		self.request.path = "/api/login";
		self.request.get_json = Mock();
		self.request.get_json.return_value = self.json;
		with patch('flask_app.resources.user.session', self.session):
			with patch('flask_app.resources.user.request', self.request):
				current_user = self.user_resource.post();
				# check that session was regenerated and current_user was set
				self.session.regenerate.assert_called()
				self.session.__setitem__.assert_called_with("current_user", current_user);
				# check current user was sent back with response
				self.assertEqual(current_user, {"ID":1, "Username":"OzzyTheGiant"});

	def test_login_throwsValidationErrorIfDataIsInvalid(self):
		self.request.path = "/api/login";
		self.request.get_json = Mock();
		self.request.get_json.return_value = {"username":"OzzyTheGiant", "password":None};
		with patch("flask_app.resources.user.request", self.request):
			with self.assertRaises(ValidationError):
				current_user = self.user_resource.post();

	def test_login_throwsEmptyRequestBodyErrorIfNoDataProvided(self):
		self.request.path = "/api/login";
		self.request.get_json = Mock(side_effect = BadRequest());
		with patch("flask_app.resources.user.request", self.request):
			with self.assertRaises(EmptyRequestBodyError):
				current_user = self.user_resource.post();

	def test_login_throwsAuthenticationErrorIfUsernameDoesNotExist(self):
		self.request.path = "/api/login";
		self.request.get_json = Mock();
		self.request.get_json.return_value = {"username":"Oz", "password":"notarealpassword"};
		with patch("flask_app.resources.user.request", self.request):
			with self.assertRaises(AuthenticationError):
				current_user = self.user_resource.post();

	def test_logout_regeneratesSessionAndReturnsNoContent(self):
		self.request.path = "/api/logout";
		with patch("flask_app.resources.user.session", self.session):
			with patch('flask_app.resources.user.request', self.request):
				response, status = self.user_resource.post();
				self.session.regenerate.assert_called();
				self.assertEqual("", response);
				self.assertEqual(204, status);

	def tearDown(self):
		try:
			test_db.drop_tables(MODELS);
		except:
			pass;
		test_db.close();


if __name__ == '__main__':
	main();