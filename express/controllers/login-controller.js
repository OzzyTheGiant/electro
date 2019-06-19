const bcrypt = require('bcrypt');

class LoginController {
	constructor(db, addCSRFToken) { 
		this.db = db;
		this.addCSRFToken = addCSRFToken;
	}

	login(request, response, next) {
		let user = null;
		return this.db.select().from(LoginController.tableName).where({Username:request.body.username})
		.then(result => {
			user = result[0];
			if (user) {
				return bcrypt.compare(request.body.password, user.Password);
			} else {
				response.clearCookie(process.env.SESSION_COOKIE);
				throw new AuthenticationError();
			}
		})
		.then(result => {
			if (!result) { // if passwords don't match
				response.clearCookie(process.env.SESSION_COOKIE);
				throw new AuthenticationError(); 
			} 
			request.session.user = { ID:user.ID, Username:user.Username };
			response.json(request.session.user);
		});
	}

	logout(request, response, next) {
		request.session.regenerate(error => {
			// must use middleware here since session was recreated
			this.addCSRFToken(request, response);
			response.status(204).end();
		});
	}
}

LoginController.tableName = "Users";

module.exports = LoginController;
