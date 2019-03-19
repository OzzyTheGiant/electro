const session = require("express-session");
const MemoryStore = require("memorystore")(session);
require('dotenv').config();

module.exports = {
	cookie:{
		maxAge:process.env.SESSION_LIFETIME * 60 * 1000,
		secure:process.env.APP_ENV === "production"
	},
	store:new MemoryStore({
		checkPeriod:process.env.SESSION_LIFETIME * 60 * 1000
	}),
	resave:false,
	saveUninitialized:false,
	unset:'destroy',
	name:process.env.SESSION_COOKIE,
	secret:Buffer.from(process.env.APP_KEY.split(":")[1], 'base64').toString()
};
