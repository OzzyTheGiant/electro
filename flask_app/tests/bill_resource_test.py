from unittest import TestCase, main;
from unittest.mock import Mock, patch;
from peewee import *;
from werkzeug.exceptions import BadRequest;
from ..errors import ValidationError, EmptyRequestBodyError;
from ..resources.bill import BillResource, bill_schema;
from ..models import Bill, User;

# BE SURE TO USE ALL OF THE MODELS because peewee links models together if they are related
# otherwise you will be getting OperationError because the db was not set up correctly, forcing you to use the
# production db instead of the in-memory sqlite db
MODELS = (Bill, User);
test_db = SqliteDatabase(":memory:");

class BillResourceTest(TestCase):
	# data to be used in test database
	user = {"ID": 1, "Username":"OzzyTheGiant", "Password":"$2a$10$Cj66BNdUZhkMvStI5jfQoetgzSvkaQIwJuIRDPIa1zgFsFPXkbqr2"}

	bills = [
		{"ID": 1, "User":1, "PaymentAmount": 80.08, "PaymentDate":"2019-06-12"},
		{"ID": 2, "User":1, "PaymentAmount": 90.08, "PaymentDate":"2019-06-15"}
	]

	newBill = {"User": 1, "PaymentAmount":88.88, "PaymentDate":"2019-06-16"};

	# mocked request object
	request = Mock();

	bill_resource = BillResource();

	def setUp(self):
		test_db.bind(MODELS, bind_refs = False, bind_backrefs = False);
		test_db.connect()
		test_db.create_tables(MODELS, safe = True);
		User.insert(self.user).execute(database = None);
		Bill.insert(self.bills).execute(database = None);

	def test_get_returnsListOfBillsFromDatabase(self):
		bills = self.bill_resource.get();
		self.assertEqual(self.bills, bills);

	def test_post_throwsValidationErrorIfDataIsInvalid(self):
		self.request.get_json = Mock();
		self.request.get_json.return_value = {"User": 1, "PaymentDate":"2019-06-16"};
		with patch("flask_app.resources.bill.request", self.request):
			with self.assertRaises(ValidationError):
				response_data, status = self.bill_resource.post();

	def test_post_throwsEmptyRequestBodyErrorIfNoDataProvided(self):
		self.request.get_json = Mock(side_effect = BadRequest());
		with patch("flask_app.resources.bill.request", self.request):
			with self.assertRaises(EmptyRequestBodyError):
				response_data, status = self.bill_resource.post();

	def test_post_addsNewBillFromDatabase(self):
		self.request.get_json = Mock();
		self.request.get_json.return_value = self.newBill;
		with patch("flask_app.resources.bill.request", self.request):
			response_data, status = self.bill_resource.post();
			# assert new bill is in database
			self.assertEqual(self.newBill, bill_schema.dump(Bill.select().where(Bill.ID == 3).get()));
			# assert that correct response was returned
			self.assertEqual(201, status);
			self.newBill.update({"ID": 3})
			self.assertEqual(self.newBill, response_data);

	def test_put_throwsValidationErrorIfDataIsInvalid(self):
		self.request.get_json = Mock();
		self.request.get_json.return_value = {"User": 1, "PaymentDate":"2019-06-16"};
		with patch("flask_app.resources.bill.request", self.request):
			with self.assertRaises(ValidationError):
				response_data, status = self.bill_resource.put(3);

	def test_put_throwsEmptyRequestBodyErrorIfNoDataProvided(self):
		self.request.get_json = Mock(side_effect = BadRequest());
		with patch("flask_app.resources.bill.request", self.request):
			with self.assertRaises(EmptyRequestBodyError):
				response_data, status = self.bill_resource.put(3);

	def test_put_updatesBillInDatabase(self):
		self.request.get_json = Mock();
		self.request.get_json.return_value = self.newBill;
		Bill.insert(self.newBill).execute(database = None);
		with patch("flask_app.resources.bill.request", self.request):
			response_data = self.bill_resource.put(3);
			# assert bill was updated in database
			self.assertEqual(self.newBill, bill_schema.dump(Bill.select().where(Bill.ID == 3).get()));
			# assert bill was returned in response
			self.assertEqual(self.newBill, response_data);

	def test_delete_removesBillFromDatabase(self):
		Bill.insert(self.newBill).execute(database = None);
		response, status = self.bill_resource.delete(3);
		# check that bill deleted is no longer in db by checking for DoesNotExist error
		with self.assertRaises(Bill.DoesNotExist):
			Bill.select().where(Bill.ID == 3).get();
		# assert response is 204 No Content
		self.assertEqual(None, response);
		self.assertEqual(204, status);
			
	def tearDown(self):
		try:
			test_db.drop_tables(MODELS);
		except:
			pass;
		test_db.close();

if __name__ == '__main__':
	main();