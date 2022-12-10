from peewee import *;
from ._base import BaseModel;

class User(BaseModel):
	ID = IntegerField(primary_key = True);
	Username = CharField(max_length = 30);
	Password = CharField(max_length = 255);

	class Meta():
		db_table = "Users";