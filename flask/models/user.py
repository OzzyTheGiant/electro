from dataclasses import dataclass
from peewee import IntegerField, CharField
from models import BaseModel


@dataclass
class User(BaseModel):
    id: int = IntegerField(primary_key=True)
    username: str = CharField(max_length=30)
    password = CharField(max_length=255)

    class Meta():
        db_table = "users"


    def __init__(self, **kwargs):
        super().__init__(**kwargs)
