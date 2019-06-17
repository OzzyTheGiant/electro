// this file will load .env file and configure all dependencies and objects needed
require('dotenv').config();
const session = require("express-session");
const MemoryStore = require("memorystore")(session);
const createLogger = require("../services/logger");
const createQueryBuilder = require("../database/mysql");

const cookieDefaultSettings = {
	maxAge:process.env.SESSION_LIFETIME * 60 * 1000,
	secure:process.env.APP_ENV === "production",
	httpOnly:true
};

module.exports = {
	logger: createLogger(
		process.env.APP_ENV, 
		process.env.LOG_FILE_PATH
	),
	db: createQueryBuilder(
		process.env.DB_CONNECTION, 
		process.env.DB_HOST, 
		process.env.DB_PORT, 
		process.env.DB_DATABASE, 
		process.env.DB_USER, 
		process.env.DB_PASSWORD,
		this.logger
	),
	sessionConfig: {
		cookie:{ ...cookieDefaultSettings},
		store:new MemoryStore({
			checkPeriod:process.env.SESSION_LIFETIME * 60 * 1000
		}),
		resave:false,
		saveUninitialized:false,
		unset:'destroy',
		name:process.env.SESSION_COOKIE,
		secret:Buffer.from(process.env.APP_KEY.split(":")[1], 'base64').toString()
	},
	csrfConfig: {
		name: process.env.XSRF_COOKIE,
		cookie:{ ...cookieDefaultSettings }
	}
};
