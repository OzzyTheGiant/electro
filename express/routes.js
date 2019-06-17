const express = require("express");
const csurf = require('csurf');
const BillController = require("./controllers/bill-controller");
const LoginController = require("./controllers/login-controller");
const { db } = require("./config/app-config");
const { addCSRFToken } = require("./middleware/middleware");
const { AuthorizationError } = require("./exceptions/exceptions");

// create router and initiate csrf protection middleware module
const csrfProtection = csurf();
const router = express.Router();

// instantiate controllers with dependencies
const billController = new BillController(db);
const loginController = new LoginController(db);

router.get("/", csrfProtection, addCSRFToken, (request, response, next) => {
	response.status(204).end();
})

router.post("/login", csrfProtection, addCSRFToken, loginController.login.bind(loginController));
router.post("/logout", csrfProtection, loginController.logout.bind(loginController));

router
	.route("/bills(/:id)?")
	.get(billController.getAll.bind(billController))
	.post(billController.add.bind(billController))
	.put(billController.update.bind(billController))
	.delete(billController.delete.bind(billController));

router.use((error, request, response, next) => {
	/* catch invalid CSRF errors */
	if (error.code === 'EBADCSRFTOKEN') {
		return next(new AuthorizationError());
	} return next(error);
});

module.exports = router;