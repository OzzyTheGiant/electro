const bcrypt = require('bcrypt');
const { addCSRFToken } = require("../middleware/middleware");

class LoginController {
	constructor(db) { this.db = db; }

	login(request, response, next) {
		this.db.select().from(LoginController.tableName).where({Username:request.body.username}).then(user => {
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

	logout(request, response, next) {
		request.session.regenerate(error => {
			// must use middleware here since session was recreated
			addCSRFToken(request, response);
			response.status(204).end();
		});
	}
}

LoginController.tableName = "Users";

module.exports = LoginController;
