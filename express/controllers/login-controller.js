const db = require('../database/mysql');
const bcrypt = require('bcrypt');

class LoginController {
	// TODO: add CSRF token

	static login(request, response, next) {
		db.select().from(LoginController.tableName).where({Username:request.body.username}).then(user => {
			if (user) {
				bcrypt.compare(request.body.password, user[0].Password).then(result => {
					if (!result) { // if passwords don't match
						response.clearCookie(process.env.SESSION_COOKIE);
						throw new AuthenticationError(); 
					} 
					request.session.user = { ID:user[0].ID, Username:user[0].Username };
					response.json(request.session.user);
				});
			} else {
				response.clearCookie(process.env.SESSION_COOKIE);
				throw new AuthenticationError();
			}
		});
	}

	static logout(request, response, next) {
		request.session.destroy(error => {
			response.status(204).end();
		});
	}
}

LoginController.tableName = "Users";

module.exports = LoginController;
