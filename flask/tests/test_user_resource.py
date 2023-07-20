import copy
from unittest import main
from flask import Flask
from flask_testing import TestCase
from peewee import SqliteDatabase
from config.bootstrap import create_app
from models.user import User
from models.bill import Bill
from resources import UserResource
from tests import load_env_variables

MODELS = (Bill, User)
test_db = SqliteDatabase(":memory:")


load_env_variables()


class TestUserResource(TestCase):
    # data to be used in test database
    user = {
        "id": 1,
        "username": "OzzyTheGiant",
        "password": "$argon2id$v=19$m=65536,t=3,p=4$ZeYABY+RVIf42Dx31DVhwg$Q4JkBb+fAIuFRq0ZN4b3GnP05U6Nw7XQWDTkBR2rtyk"  # noqa: E501
    }

    # request body
    json = {"username": "OzzyTheGiant", "password": "notarealpassword"}

    user_resource = UserResource()
    auth_headers = None


    def create_app(self) -> Flask:
        return create_app()


    def setUp(self):
        test_db.bind(MODELS, bind_refs = False, bind_backrefs = False)
        test_db.connect()
        test_db.create_tables(MODELS, safe = True)
        User.insert(self.user).execute(database = None)

        # The first user in the users list will be used to log in
        response = self.client.post("/api/login", json = self.json)

        cookies = response.headers.getlist("Set-Cookie")
        cookies = [cookie.split(";")[0].split("=")[1] for cookie in cookies]

        self.auth_headers = {
            "Cookie": self.app.config["JWT_ACCESS_COOKIE_NAME"] + "=" + cookies[0],
            "X-CSRF-TOKEN": cookies[1]
        }


    def test_user_can_log_in(self) -> None:
        response = self.client.post("/api/login", json = self.json)
        assert response.status_code == 200
        assert response.get_json() is None
        assert self.auth_headers["Cookie"] is not None
        assert self.auth_headers["X-CSRF-TOKEN"] is not None


    def test_login_password_is_invalid(self):
        credentials = copy.deepcopy(self.json)
        credentials["password"] = "wrong_password"
        response = self.client.post("/api/login", json = credentials)
        assert response.status_code == 401
        assert response.get_json()["message"] is not None


    def test_missing_credentials_returns_error_400(self) -> None:
        response = self.client.post("/api/login", json = {})
        assert response.status_code == 400
        assert type(response.get_json()["message"]) == dict


    def test_login_username_is_invalid(self) -> None:
        credentials = copy.deepcopy(self.json)
        credentials["username"] = "wrong_username"
        response = self.client.post("/api/login", json = credentials)
        assert response.status_code == 401
        assert response.get_json()["message"] is not None


    def test_user_can_log_out(self) -> None:
        response = self.client.post("/api/logout", headers = self.auth_headers)
        assert response.status_code == 204
        assert response.get_json() is None
        print(response.headers.get("Set-Cookie"))
        assert len(response.headers.get("Set-Cookie")) > 0


    def tearDown(self):
        try:
            test_db.drop_tables(MODELS)
        except:
            pass
        test_db.close()


if __name__ == '__main__':
    main()
