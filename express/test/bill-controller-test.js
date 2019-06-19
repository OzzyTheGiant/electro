const sinon = require("sinon");
const assert = require("chai").assert;
const BillController = require("../controllers/bill-controller");
const { NotFoundError } = require("../exceptions/exceptions");

describe("Bill Controller", () => {
	const data = [
		{"ID":1, "User":1, "PaymentAmount":80.08, "PaymentDate":"2019-06-12"},
		{"ID":2, "User":1, "PaymentAmount":81.08, "PaymentDate":"2019-06-13"}
	];
	
	// request, respone and next variables to be reset after each test
	const request = {
		body:{"User":1, "PaymentAmount":91.08, "PaymentDate":"2019-06-14"},
		params: {}
	};

	const response = {
		status:sinon.stub().returnsThis(),
		json:sinon.stub().returnsThis(),
		end:sinon.stub().returns(null)
	};

	const next = sinon.stub();

	// db dependency
	const db = sinon.stub();
	db.returns(db);
	const dbMethods = {
		select:sinon.stub().returnsThis(),
		from:sinon.stub().returnsThis(),
		orderBy:sinon.stub().resolves(data),
		insert:sinon.stub().returnsThis(),
		into:sinon.stub().resolves([3]),
		where:sinon.stub().returnsThis(),
		update:sinon.stub(),
		del:sinon.stub()
	};

	// assign all database method stubs to mock database
	for (var key in dbMethods) db[key] = dbMethods[key];

	const billController = new BillController(db);

	// for promises' catch method in the tests
	const errorHandler = error => { throw error; };

	it("fetches all bills from database", () => {
		return billController.getAll(request, response, next).then(() => {
			assert.equal(db.select.callCount, 1);
			assert.equal(db.from.getCall(0).args[0], BillController.tableName);
			assert.deepEqual(db.orderBy.getCall(0).args, ['PaymentDate', 'DESC'])
			assert.equal(response.json.getCall(0).args[0], data);
		}).catch(errorHandler);
	});

	it("adds new bill to database", () => {
		return billController.add(request, response, next).then(() => {
			assert.equal(db.insert.callCount, 1);
			assert.equal(db.into.getCall(0).args[0], BillController.tableName);
			assert.equal(response.status.getCall(0).args[0], 201);
			assert.deepEqual(response.json.getCall(0).args[0], {...request.body, ID: 3});
		}).catch(errorHandler);
	});

	it("updates bill in database", () => {
		request.body.ID = 3;
		request.params.id = 3;
		db.update.returns(new Promise((resolve, reject) => request.params.id == 4 ? resolve(null) : resolve(1)));
		return billController.update(request, response, next).then(() => {
			assert.equal(db.getCall(0).args[0], BillController.tableName);
			assert.deepEqual(db.where.getCall(0).args, ["ID", "=", request.params.id]);
			assert.deepEqual(db.update.getCall(0).args[0], request.body);
			assert.deepEqual(response.json.getCall(0).args[0], request.body);
		});
	});

	it("sends NotFoundException if no bill found when updating a bill", () => {
		request.body.ID = 4;
		request.params.id = 4;
		db.update.returns(new Promise((resolve, reject) => request.params.id == 4 ? resolve(null) : resolve(1)));
		return billController.update(request, response, next).then(() => {
			assert.equal(db.getCall(0).args[0], BillController.tableName);
			assert.deepEqual(db.where.getCall(0).args, ["ID", "=", request.params.id]);
			assert.deepEqual(db.update.getCall(0).args[0], request.body);
			assert.isTrue(next.getCall(0).args[0] instanceof NotFoundError);
		}).catch(errorHandler);
	});
	
	it("deletes bill from database", () => {
		request.body.ID = 3;
		request.params.id = 3;
		db.del.returns(new Promise((resolve, reject) => request.params.id == 4 ? resolve(null) : resolve(1)));
		return billController.delete(request, response, next).then(() => {
			assert.equal(db.getCall(0).args[0], BillController.tableName);
			assert.deepEqual(db.where.getCall(0).args, ["ID", "=", request.params.id]);
			assert.equal(db.del.callCount, 1);
			assert.deepEqual(response.status.getCall(0).args[0], 204);
			assert.equal(response.end.callCount, 1);
		}).catch(errorHandler);
	});

	it("sends NotFoundException if no bill found when deleting a bill", () => {
		request.body.ID = 4;
		request.params.id = 4;
		db.del.returns(new Promise((resolve, reject) => request.params.id == 4 ? resolve(null) : resolve(1)));
		return billController.delete(request, response, next).then(() => {
			assert.equal(db.getCall(0).args[0], BillController.tableName);
			assert.deepEqual(db.where.getCall(0).args, ["ID", "=", request.params.id]);
			assert.equal(db.del.callCount, 1);
			assert.isTrue(next.getCall(0).args[0] instanceof NotFoundError);
		}).catch(errorHandler);
	});

	afterEach(() => {
		db.resetHistory();
		next.resetHistory();
		for (var key in response) response[key].resetHistory();
		for (var key in dbMethods) dbMethods[key].resetHistory();
	});
});