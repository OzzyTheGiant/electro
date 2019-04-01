import os;
from peewee import MySQLDatabase, Model;

db = MySQLDatabase(
	database = os.getenv("DB_DATABASE", "Electro"),
	host = os.getenv("DB_HOST"),
	port = int(os.getenv("DB_PORT", 3306)),
	user = os.getenv("DB_USERNAME"),
	password = os.getenv("DB_PASSWORD")
);

class BaseModel(Model):
	"""A base model that will define the database to be used"""
	class Meta:
		database = db;