"""App models"""
from django.contrib.auth.models import AbstractBaseUser;
from django.db import models;

# will not be using django's auth User model due to field requirements
class User(AbstractBaseUser): # implements only password and last_login fields as well as a few methods
	"""Model for database table Users"""
	ID = models.IntegerField(primary_key = True);
	Username = models.CharField(max_length = 30, unique = True);
	Password = models.CharField(max_length = 255);

	USERNAME_FIELD = 'Username';
	last_login = None; # this is a default db column from AbstractBaseUser that is unnecessary

	class Meta:
		db_table = "Users";
		verbose_name = "user";
		verbose_name_plural = "users";

class Bill(models.Model):
	"""Model for database table Bills"""
	ID = models.AutoField(primary_key = True);
	User = models.ForeignKey(User, on_delete = models.PROTECT, db_column = "User");
	PaymentAmount = models.DecimalField(max_digits = 5, decimal_places = 2);
	PaymentDate = models.DateField();

	class Meta:
		db_table = "Bills";
		ordering = ('-PaymentDate', );
