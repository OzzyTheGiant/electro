import dotenv from "dotenv"
import createLogger from "@app/services/logger"
import createQueryBuilder from "@app/database/postgresql"

dotenv.config()

// const cookieDefaultSettings = {
// 	maxAge: process.env.SESSION_LIFETIME * 60 * 1000,
// 	secure: process.env.APP_ENV === "production",
// 	httpOnly: true
// };

export const logger = createLogger(
    process.env.APP_ENV,
    process.env.LOG_FILE_PATH
)

export const db = createQueryBuilder(
    process.env.DB_CONNECTION,
    {
        host: process.env.DB_HOST,
        port: parseInt(process.env.DB_PORT),
        database: process.env.DB_DATABASE,
        user: process.env.DB_USER,
        password: process.env.DB_PASSWORD,
    },
    logger
)

