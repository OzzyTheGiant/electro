const express = require("express");
const Bill = require("./controllers/bill-controller");

const router = express.Router();

router
	.route("/bills(/:id)?")
	.get(Bill.getAll)
	.post(Bill.add)
	.put(Bill.update)
	.delete(Bill.delete);

module.exports = router;