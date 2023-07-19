from typing import Union
from flask_jwt_extended import JWTManager
from models.user import User

jwt = JWTManager()


@jwt.user_identity_loader
def user_identity_lookup(identity: Union[dict, str]) -> dict:
    return { "id": identity["id"], "username": identity["username"] }


@jwt.user_lookup_loader
def user_lookup_callback(_jwt_header: dict, jwt_data: dict) -> Union[User, None]:
    id = jwt_data["sub"]["id"]
    username = jwt_data["sub"]["username"]
    return User.get_or_none(User.id == id, User.username == username)
