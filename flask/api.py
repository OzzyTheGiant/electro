from flask import jsonify, current_app


class ElectroAPI():
    def handle_error(self, error):
        """Global error handler for all API routes"""
        if hasattr(error, 'description'):  # check if it's a custom error with description text
            if hasattr(error, 'metadata'):  # check if custom error class allows metadata
                current_app.logger.error(error.description, error.metadata)
            else:
                current_app.logger.error(error.description)
        else:  # possible uncaught or unexpected exceptions
            current_app.logger.error(error.args[0])  # message
        return (jsonify({"message": error.description}), error.code)
