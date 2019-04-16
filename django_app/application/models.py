"""App models"""
from django.contrib.auth.models import AbstractUser;
from django.db import models;

# will not be using django's auth User model due to field requirements
class User(AbstractUser):
	"""Model for database table Users"""
	ID = models.IntegerField(primary_key = True);
	username = models.CharField(max_length = 30, db_column = "Username", unique = True);
	password = models.CharField(max_length = 255, db_column = "Password");

	class Meta:
		db_table = "Users";

class Bill(models.Model):
	"""Model for database table Bills"""
	ID = models.IntegerField(primary_key = True);
	User = models.ForeignKey(User, on_delete = models.PROTECT, db_column = "User");
	PaymentAmount = models.DecimalField(max_digits = 5, decimal_places = 2);
	PaymentDate = models.DateField();

	class Meta:
		db_table = "Bills";
		ordering = ('-PaymentDate', );
