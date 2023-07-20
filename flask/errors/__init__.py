from flask import current_app
from errors.exceptions import DatabaseError

DatabaseError = DatabaseError


def error_handler(error: Exception):
    """Global error handler for all API routes"""

    message = None

    if hasattr(error, 'description'):  # check if it's a custom error with description text
        if hasattr(error, 'metadata'):  # check if custom error class allows metadata
            current_app.logger.error(error.description, error.metadata)
        else:
            current_app.logger.error(error.description)

        message = error.description
    elif hasattr(error, "message"):
        current_app.logger.error(error.message)
        message = error.message
    else:  # possible uncaught or unexpected exceptions
        current_app.logger.error(error.args[0])  # message
        message = error.args[0]

    if message is None: message = "Server Error: Try again or contact for support"
    code = error.code if hasattr(error, "code") else 500

    return ({ "message": message }, code)
