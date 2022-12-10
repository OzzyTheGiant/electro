import dotenv from "dotenv"
import createLogger from "@app/services/logger"
// const createQueryBuilder = require("../database/mysql");

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
// db: createQueryBuilder(
// 	process.env.DB_CONNECTION, 
// 	process.env.DB_HOST, 
// 	process.env.DB_PORT, 
// 	process.env.DB_DATABASE, 
// 	process.env.DB_USER, 
// 	process.env.DB_PASSWORD,
// 	this.logger
// ),
// csrfConfig: {
// 	name: process.env.XSRF_COOKIE,
// 	cookie:{ ...cookieDefaultSettings }
// }

