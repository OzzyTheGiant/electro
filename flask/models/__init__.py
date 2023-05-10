import json
from typing import Any, Dict, List, Tuple, Union, cast
from dataclasses import asdict
from datetime import datetime
from peewee import PostgresqlDatabase, Model

database = PostgresqlDatabase(None)


class SerializerMixin():
    def as_dict(
        self,
        date_properties: Union[List[str], None] = None,
        time_properties: Union[List[str], None] = None,
        enum_properties: Union[List[str], None] = None,
        decimal_properties: Union[List[str], None] = None,
        nested_models: Union[List[Tuple[str, Any]], None] = None
    ) -> dict:
        data = cast(Dict[str, Any], asdict(cast(Any, self)))

        if date_properties:
            for key in date_properties:
                data[key] = datetime.strftime(data[key], "%Y-%m-%d")

        if time_properties:
            for key in time_properties:
                data[key] = datetime.strftime(data[key], "%Y-%m-%d %H:%M:%S")

        if enum_properties:
            for key in enum_properties:
                data[key] = data[key].value

        if decimal_properties:
            for key in decimal_properties:
                data[key] = float(data[key])

        if nested_models:
            for value in nested_models:
                data[value[0]] = [item.as_dict() for item in value[1]]

        return data


    def as_json(self) -> str:
        return json.dumps(self.as_dict())



class BaseModel(Model, SerializerMixin):
    """A base model that will define the database to be used"""
    class Meta:
        database = database
