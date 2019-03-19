require('dotenv').config();

function deleteSessionCookie(request, response, next) {
	if (request.cookies[process.env.SESSION_COOKIE] && !request.session.user) {
		response.clearCookie(process.env.SESSION_COOKIE);        
    } next();
};

module.exports = { deleteSessionCookie };
