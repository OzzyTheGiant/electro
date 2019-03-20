const { csrfConfig } = require("../config/session-config");
require('dotenv').config();

function deleteSessionCookie(request, response, next) {
	if (request.cookies[process.env.SESSION_COOKIE] && !request.session.user) {
		response.clearCookie(process.env.SESSION_COOKIE);        
    } next();
};

function addCSRFToken(request, response, next) {
	response.cookie(process.env.XSRF_COOKIE, request.csrfToken(), csrfConfig.cookie);
	if (next) next();
}

module.exports = { deleteSessionCookie, addCSRFToken };
