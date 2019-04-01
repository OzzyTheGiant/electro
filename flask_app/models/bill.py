from peewee import *;
from .user import User;
from ._base import BaseModel;

class Bill(BaseModel):
	ID = PrimaryKeyField();
	User = ForeignKeyField(User, db_column="ID", related_name = "Bills");
	PaymentAmount = DecimalField(max_digits = 5, decimal_places = 2);
	PaymentDate = DateField();

	class Meta():
		db_table = "Bills";
		order_by = ("-PaymentDate");
		only_save_dirty = True;