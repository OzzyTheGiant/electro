from werkzeug.exceptions import HTTPException


class ValidationError(Exception):
    """Error for when a specific field of data is invalid"""

    def __init__(self, metadata=None):
        self.description = "The data provided is invalid"
        self.code = 400
        self.loggable = False

        if metadata:
            self.description = ""
            for field, messages in metadata.items():
                for message in messages:
                    self.description += "{}: {} ".format(field, message)
                break  # we will only show one message at a time to keep notifications small in UI


class NotFoundError(Exception):
    """
    Error for when a specific field of data was not found in database,
    which is different from HTTP Not Found error
    """

    def __init__(self, item="item"):
        self.description = "The specified {} could not be found".format(item)
        self.code = 404
        self.loggable = False


class AuthorizationError(HTTPException):
    """
    Error for when user is not authorized to perform a specific rest api endpoint
    or CSRF token not valid
    """

    def __init__(self, metadata=None):
        self.description = "You are not authorized to perform this action"
        self.code = 403
        self.loggable = False
        self.metadata = metadata


class AuthenticationError(HTTPException):
    """Error for when credentials are incorrect"""

    def __init__(self):
        self.description = "Username or password is incorrect"
        self.code = 401
        self.loggable = False


class DatabaseError(Exception):
    """
    Generic error for database query failures;
    metadata will provide hidden message which will be logged
    """

    def __init__(self, metadata):
        self.description = "Something went wrong while querying the database"
        self.code = 500
        self.loggable = True
        self.metadata = metadata


class EmptyRequestBodyError(HTTPException):
    """Error for when HTTP request did not include any json data"""

    def __init__(self):
        self.description = "No data was provided"
        self.code = 400
