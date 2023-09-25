import os
from typing import Dict, Any
from django.test import TestCase
from electro.models import Bill, User


class AppTestCase(TestCase):
    csrf_token = None
    jwt_string = None

    credentials = {
        "username": "OzzyTheGiant",
        "password": "notarealpassword"
    }

    def get_headers(self) -> Dict[str, Any]:
        return {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": self.csrf_token or "",
            "Cookie": (
                f"{os.environ['JWT_ACCESS_COOKIE_NAME']}={self.jwt_string}; " +  # noqa W504
                f"{os.environ['JWT_CSRF_COOKIE_NAME']}={self.csrf_token}"
            )
        }

    def setUp(self):
        User.objects.create(
            username='OzzyTheGiant',
            password='$argon2id$v=19$m=65536,t=3,p=4$ZeYABY+RVIf42Dx31DVhwg$Q4JkBb+fAIuFRq0ZN4b3GnP05U6Nw7XQWDTkBR2rtyk'  # noqa: E501
        )

        response = self.client.get("/api/home")
        self.csrf_token = response.cookies[os.environ['JWT_CSRF_COOKIE_NAME']].value

        response = self.client.post("/api/login", self.credentials)
        self.jwt_string = response.cookies[os.environ["JWT_ACCESS_COOKIE_NAME"]].value


class LoginTestCase(AppTestCase):
    def test_user_can_login_successfully(self):
        assert self.jwt_string is not None


    def test_user_can_logout_successfully(self):
        response = self.client.post("/api/logout", **self.get_headers())
        jwt_cookie = response.cookies[os.environ["JWT_ACCESS_COOKIE_NAME"]]
        assert response.status_code == 204
        assert not jwt_cookie.value  # the jwt cookie will return empty string


class BillsTestCase(AppTestCase):
    def test_bills_can_be_fetched(self):
        response = self.client.get("/api/bills", **self.get_headers())
        assert response.status_code == 200


    def test_bills_can_be_created(self):
        data = {
            "user_id": 1,
            "payment_amount": 55.55,
            "payment_date": "2022-01-01"
        }

        response = self.client.post("/api/bills", data = data, **self.get_headers())
        bill = Bill.objects.first()
        result = response.json()

        assert response.status_code == 201
        assert bill is not None
        assert float(bill.payment_amount) == data["payment_amount"]
        assert result["payment_amount"] == data["payment_amount"]
        assert result["payment_date"] == data["payment_date"]


    def test_bills_can_be_edited(self):
        user = User.objects.first()
        bill = Bill.objects.create(
            user_id = user,
            payment_amount = 55.55,
            payment_date = "2022-01-01"
        )

        bill_id = bill.id if bill else 0
        user_id = user.id if user else 0

        data = {
            "id": bill_id,
            "user_id": user_id,
            "payment_amount": 66.66,
            "payment_date": "2022-02-01"
        }

        response = self.client.put(
            f"/api/bills/{bill_id}",
            data = data,
            content_type = "application/json",
            **self.get_headers()
        )

        bill = Bill.objects.get(id = bill_id)
        result = response.json()

        assert response.status_code == 200
        assert bill is not None
        assert float(bill.payment_amount) == data["payment_amount"]
        assert result["payment_amount"] == data["payment_amount"]
        assert result["payment_date"] == data["payment_date"]


    def test_bills_can_be_deleted(self):
        bill = Bill.objects.create(
            user_id = User.objects.first(),
            payment_amount = 55.55,
            payment_date = "2022-01-01"
        )

        response = self.client.delete(f"/api/bills/{bill.id if bill else 0}", **self.get_headers())
        assert response.status_code == 204
        assert not Bill.objects.filter(id = bill.id).exists()
