from dataclasses import dataclass
from peewee import PrimaryKeyField, ForeignKeyField, DecimalField, DateField
from marshmallow import Schema, fields, validate
from models import BaseModel
from models.user import User


@dataclass
class Bill(BaseModel):
    id: int = PrimaryKeyField()
    # column_name must be set to prevent peewee from using <field>_id column name
    user_id: int = ForeignKeyField(User, backref = "bills")
    payment_amount: float = DecimalField(max_digits = 5, decimal_places = 2)
    payment_date: str = DateField()

    class Meta():
        db_table = "bills"
        order_by = ("-payment_date")
        only_save_dirty = True


    def __init__(self, **kwargs):
        super().__init__(**kwargs)


    def as_dict(self):
        data = super().as_dict(
            decimal_properties = ["payment_amount"],
            date_properties = ["payment_date"]
        )

        data["user_id"] = data["user_id"]["id"]
        return data


class BillSchema(Schema):
    id = fields.Int()
    user_id = fields.Int(required=True)
    payment_amount = fields.Float(required=True, validate=validate.Range(min=0.01, max=99999.99))
    payment_date = fields.Date(required=True)
