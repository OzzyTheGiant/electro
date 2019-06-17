const Bill = require('../models/Bill');
const { DatabaseError, NotFoundError } = require('../exceptions/exceptions');

class BillController {
	constructor(db) {
		this.tableName = "Bills";
		this.db = db;
	}

	/** Get all bills from database	*/
	getAll(request, response, next) {
		this.db.select().from(BillController.tableName).orderBy('PaymentDate', 'DESC').then(results => {
			response.json(results);
		}).catch(BillController.errorHandler(next));
	}

	/** Insert new bills into database */
	add(request, response, next) {
		const bill = Bill.withValidatedData(request.body);
		this.db.insert(bill).into(BillController.tableName).then(result => {
			if (!result) return next(new NotFoundError("bill"));
			bill.ID = result[0];
			response.status(201).json(bill);
		}).catch(BillController.errorHandler(next));
	}

	/** Update bills in database */
	update(request, response, next) {
		const bill = Bill.withValidatedData(request.body);
		this.db(BillController.tableName).where('ID', '=', request.params.id).update(bill).then(result => {
			if (!result) return next(new NotFoundError("bill"));
			response.json(bill);
		}).catch(BillController.errorHandler(next));
	}

	/** Remove bill from database */
	delete(request, response, next) {
		this.db(this.tableName).where('ID', '=', request.params.id).del().then(result => {
			if (!result) return next(new NotFoundError("bill"));
			response.status(204).end();
		}).catch(BillController.errorHandler(next));
	}

	/** Creates an error handling function for Knex, which will call error handling middleware */
	static errorHandler(next) {
		return (error, object) => {
			next(new DatabaseError(error.message))
		};
	}
};

BillController.tableName = "Bills";

module.exports = BillController;