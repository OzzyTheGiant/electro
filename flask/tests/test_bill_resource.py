import copy
from unittest import main
from flask_testing import TestCase
from peewee import SqliteDatabase
from resources.bill import BillResource
from models.bill import Bill
from models.user import User
from config.bootstrap import create_app
from tests import load_env_variables

load_env_variables()

# BE SURE TO USE ALL OF THE MODELS because peewee links models together if they are related
# otherwise you will be getting OperationError because the db was not set up correctly, forcing you
# to use the production db instead of the in-memory sqlite db
MODELS = (Bill, User)
test_db = SqliteDatabase(":memory:")


class TestBillResource(TestCase):
    # data to be used in test database
    user = {
        "id": 1,
        "username": "OzzyTheGiant",
        "password": "$argon2id$v=19$m=65536,t=3,p=4$ZeYABY+RVIf42Dx31DVhwg$Q4JkBb+fAIuFRq0ZN4b3GnP05U6Nw7XQWDTkBR2rtyk"  # noqa: E501
    }

    bills = [
        {"id": 1, "user_id": 1, "payment_amount": 80.08, "payment_date": "2019-06-12"},
        {"id": 2, "user_id": 1, "payment_amount": 90.08, "payment_date": "2019-06-15"}
    ]

    new_bill = {"user_id": 1, "payment_amount": 88.88, "payment_date": "2019-06-16"}

    bill_resource = BillResource()
    auth_headers = None


    def create_app(self) -> None:
        return create_app()


    def setUp(self) -> None:
        test_db.bind(MODELS, bind_refs = False, bind_backrefs = False)
        test_db.connect()
        test_db.create_tables(MODELS, safe = True)
        User.insert(self.user).execute(database = None)
        Bill.insert(self.bills).execute(database = None)

        # The first user in the users list will be used to log in
        response = self.client.post("/api/login", json = {
            "username": self.user["username"],
            "password": "notarealpassword"
        })

        cookies = response.headers.getlist("Set-Cookie")
        cookies = [cookie.split(";")[0].split("=")[1] for cookie in cookies]

        self.auth_headers = {
            "Cookie": self.app.config["JWT_ACCESS_COOKIE_NAME"] + "=" + cookies[0],
            "X-CSRF-TOKEN": cookies[1]
        }


    def test_list_of_bills_can_be_fetched(self) -> None:
        response = self.client.get("/api/bills", headers = self.auth_headers)
        assert response.status_code == 200
        assert self.bills == response.get_json()


    def test_bill_can_be_added(self) -> None:
        response = self.client.post("/api/bills", json = self.new_bill, headers = self.auth_headers)
        data = response.get_json()
        assert response.status_code == 201
        assert self.new_bill["user_id"] == data["user_id"]
        assert self.new_bill["payment_amount"] == data["payment_amount"]
        assert self.new_bill["payment_date"] == data["payment_date"]


    def test_error_400_occurs_when_bill_data_missing_properties(self) -> None:
        response = self.client.post("/api/bills", json = {}, headers = self.auth_headers)
        assert response.status_code == 400
        assert type(response.get_json()["message"]) == dict


    def test_error_400_occurs_when_bill_data_properties_are_invalid(self) -> None:
        new_bill = copy.deepcopy(self.new_bill)
        new_bill["payment_amount"] = "A"
        response = self.client.post("/api/bills", json = new_bill, headers = self.auth_headers)
        assert response.status_code == 400
        assert response.get_json()["message"] is not None


    def test_bill_can_be_updated(self) -> None:
        bill = copy.deepcopy(self.bills[0])
        bill["payment_amount"] = 99.99
        response = self.client.put("/api/bills/2", json = bill, headers = self.auth_headers)
        assert response.status_code == 200
        assert response.get_json()["payment_amount"] == bill["payment_amount"]


    def test_error_400_occurs_when_existing_bill_data_missing_properties(self) -> None:
        response = self.client.put("/api/bills", json = {}, headers = self.auth_headers)
        assert response.status_code == 400
        assert type(response.get_json()["message"]) == dict


    def test_error_400_occurs_when_existing_bill_data_properties_are_invalid(self) -> None:
        new_bill = copy.deepcopy(self.new_bill)
        new_bill["payment_amount"] = "A"
        response = self.client.put("/api/bills", json = new_bill, headers = self.auth_headers)
        assert response.status_code == 400
        assert response.get_json()["message"] is not None


    def test_bill_can_be_deleted(self) -> None:
        response = self.client.delete("/api/bills/2", headers = self.auth_headers)
        data = response.get_data(as_text = True)
        assert response.status_code == 204
        assert data == ""


    def tearDown(self):
        try:
            test_db.drop_tables(MODELS)
        except:
            pass
        test_db.close()


if __name__ == '__main__':
    main()
