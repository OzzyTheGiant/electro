const db = require('../database/mysql');
const Bill = require('../models/Bill');
const { DatabaseError, NotFoundError } = require('../exceptions/exceptions');

class BillController {
	/** Get all bills from database	*/
	static getAll(request, response, next) {
		db.select().from(BillController.tableName).orderBy('PaymentDate', 'DESC').then(results => {
			response.json(results);
		}).catch(BillController.errorHandler(next));
	}

	/** Insert new bills into database */
	static add(request, response, next) {
		const bill = Bill.withValidatedData(request.body);
		db.insert(bill).into(BillController.tableName).then(result => {
			if (!result) return next(new NotFoundError("bill"));
			bill.ID = result[0];
			response.status(201).json(bill);
		}).catch(BillController.errorHandler(next));
	}

	/** Update bills in database */
	static update(request, response, next) {
		const bill = Bill.withValidatedData(request.body);
		db(BillController.tableName).where('ID', '=', request.params.id).update(bill).then(result => {
			if (!result) return next(new NotFoundError("bill"));
			response.json(bill);
		}).catch(BillController.errorHandler(next));
	}

	/** Remove bill from database */
	static delete(request, response, next) {
		db(BillController.tableName).where('ID', '=', request.params.id).del().then(result => {
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