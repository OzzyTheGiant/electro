from flask_wtf import CSRFProtect
from flask_wtf.csrf import CSRFError
from ..errors import AuthorizationError


class CSRFProtectionExtension(CSRFProtect):
    """Extends CSRFProtect class from flask_wtf to output custom error message for REST APIs"""

    def protect(self):
        try:
            super().protect()
        except CSRFError as error:
            raise AuthorizationError(metadata=error.description)
