const express = require("express");
const csurf = require('csurf');
const BillController = require("./controllers/bill-controller");
const LoginController = require("./controllers/login-controller");
const { addCSRFToken } = require("./middleware/middleware");
const { AuthorizationError } = require("./exceptions/exceptions");

const csrfProtection = csurf();
const router = express.Router();

router.get("/", csrfProtection, addCSRFToken, (request, response, next) => {
	response.status(204).end();
})

router.post("/login", csrfProtection, addCSRFToken, LoginController.login);
router.get("/logout", csrfProtection, LoginController.logout);

router
	.route("/bills(/:id)?")
	.get(BillController.getAll)
	.post(BillController.add)
	.put(BillController.update)
	.delete(BillController.delete);

router.use((error, request, response, next) => {
	/* catch invalid CSRF errors */
	if (error.code === 'EBADCSRFTOKEN') {
		return next(new AuthorizationError());
	} return next(error);
});

module.exports = router;