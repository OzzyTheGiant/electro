"""App models"""
from django.contrib.auth.models import AbstractBaseUser
from django.db import models


class User(AbstractBaseUser):
    """Model for database table Users. implements only password and last_login fields as well as
    a few methods"""

    id = models.AutoField(primary_key = True)
    username = models.CharField(max_length = 30, unique = True)
    password = models.CharField(max_length = 255)

    USERNAME_FIELD = "username"
    last_login = None
    # this is a default db column from AbstractBaseUser that is unnecessary

    class Meta:
        db_table = "users"
        verbose_name = "user"
        verbose_name_plural = "users"


class Bill(models.Model):
    """Model for database table Bills"""

    id = models.AutoField(primary_key = True)
    user_id = models.ForeignKey(User, on_delete = models.CASCADE, db_column = "user_id")
    payment_amount = models.DecimalField(max_digits = 5, decimal_places = 2)
    payment_date = models.DateField()

    class Meta:
        db_table = "bills"
        ordering = ("-payment_date",)
