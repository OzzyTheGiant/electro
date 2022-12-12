from peewee import PrimaryKeyField, ForeignKeyField, DecimalField, DateField
from .user import User
from ._base import BaseModel


class Bill(BaseModel):
    ID = PrimaryKeyField()
    # column_name must be set to prevent peewee from using <field>_id column name
    User = ForeignKeyField(User, column_name="User", backref="Bills")
    PaymentAmount = DecimalField(max_digits=5, decimal_places=2)
    PaymentDate = DateField()

    class Meta():
        db_table = "Bills"
        order_by = ("-PaymentDate")
        only_save_dirty = True
