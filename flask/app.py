import os
from dotenv import load_dotenv
from config.bootstrap import create_app

load_dotenv("./.env")  # get environment variables

app = create_app()

if __name__ == "__main__":
    app.run(port = os.environ["APP_PORT"])
