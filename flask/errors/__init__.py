from flask import current_app, jsonify
from errors.exceptions import DatabaseError

DatabaseError = DatabaseError


def error_handler(error: Exception):
    """Global error handler for all API routes"""
    if hasattr(error, 'description'):  # check if it's a custom error with description text
        if hasattr(error, 'metadata'):  # check if custom error class allows metadata
            current_app.logger.error(error.description, error.metadata)
        else:
            current_app.logger.error(error.description)
    else:  # possible uncaught or unexpected exceptions
        current_app.logger.error(error.args[0])  # message

    message = error.description if hasattr(error, "description") else error.args[0]
    code = error.code if hasattr(error, "code") else 500

    return (jsonify({ "message": message }), code)
