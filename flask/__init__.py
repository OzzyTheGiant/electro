import os
if (os.getenv("APP_ENV") is not None):  # to avoid loading these modules during unit testing
    from models import Bill
    from config.api import ElectroAPI
    from config import config

    Bill = Bill
    ElectroAPI = ElectroAPI
    config = config
