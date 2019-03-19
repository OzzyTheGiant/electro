const express = require("express");
const BillController = require("./controllers/bill-controller");
const LoginController = require("./controllers/login-controller");

const router = express.Router();

router.post("/login", LoginController.login);
router.get("/logout", LoginController.logout);

router
	.route("/bills(/:id)?")
	.get(BillController.getAll)
	.post(BillController.add)
	.put(BillController.update)
	.delete(BillController.delete);

module.exports = router;