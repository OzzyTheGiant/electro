const { sessionConfig, csrfConfig } = require("../config/app-config");

function deleteSessionCookie(request, response, next) {
	if (request.cookies[sessionConfig.name] && !request.session.user) {
		response.clearCookie(sessionConfig.name);        
    } next();
};

function addCSRFToken(request, response, next) {
	response.cookie(csrfConfig.name, request.csrfToken(), csrfConfig.cookie);
	if (next) next();
}

module.exports = { deleteSessionCookie, addCSRFToken };
