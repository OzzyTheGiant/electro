from typing import Any, Union
from werkzeug.exceptions import InternalServerError


class DatabaseError(InternalServerError):
    """
    Generic error for database query failures;
    metadata will provide hidden message which will be logged
    """

    def __init__(self, metadata: Union[Any, None] = None, **kwargs):
        super().__init__(**kwargs)
        self.description = "Something went wrong while querying the database"
        self.code = 500
        self.loggable = True
        self.metadata = metadata
