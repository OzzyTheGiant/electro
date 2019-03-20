const express = require("express");
const session = require("express-session");
const cookieParser = require("cookie-parser");
const bodyParser = require("body-parser");
const router = require("./express/routes");
const { sessionConfig } = require("./express/config/session-config");
const { deleteSessionCookie } = require("./express/middleware/middleware");
const { NotFoundError } = require("./express/exceptions/exceptions");

const app = express();

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
	console.error(error); // TODO: change log system based on environment
	response.status(error.code || 500).json({message:error.message});
});

app.listen(80, function() { 
	console.log("Server listening on port 80");
});