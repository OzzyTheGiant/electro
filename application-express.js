const express = require("express");
const session = require("express-session");
const cookieParser = require("cookie-parser");
const bodyParser = require("body-parser");
const logger = require("./express/services/logger");
const router = require("./express/routes");
const { sessionConfig } = require("./express/config/session-config");
const { deleteSessionCookie } = require("./express/middleware/middleware");
const { NotFoundError } = require("./express/exceptions/exceptions");

const app = express();

/* Log server shut down events */
process.on('SIGINT', () => logger.notice('Server is shutting down manually'));
process.on('exit', code => logger.notice(`Server is shutting down with code ${code}`));
process.on('uncaughtException', error => {
	logger.error(error);
	process.exit(1);
});

/* Middlewares and Router */
if (process.env.APP_ENV !== "local") app.disable('x-powered-by');
app.use(bodyParser.json()); // decode json as javascript object
app.use(cookieParser());
app.use(session(sessionConfig));
app.use(deleteSessionCookie); // delete session cookie when not logged in
app.use("/api", router); // load router with all controllers
app.use((request, respose, next) => {
	next(new NotFoundError("API route")); // Error 404 - Not Found handler
});
app.use((error, request, response, next) => { // global error handler
	if (error.loggable) logger.error(error, error.metadata);
	response.status(error.code || 500).json({message:error.message});
});

/* Start server */
app.listen(4000, function() { 
	logger.notice("Server listening on port " + 4000);
});
