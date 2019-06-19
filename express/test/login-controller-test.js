const sinon = require("sinon");
const assert = require("chai").assert;
const LoginController = require("../controllers/login-controller");
const { AuthenticationError } = require("../exceptions/exceptions");

describe("Login Controller", () => {
	const data = [{ID: 1, Username:"OzzyTheGiant", Password:"$2a$10$Cj66BNdUZhkMvStI5jfQoetgzSvkaQIwJuIRDPIa1zgFsFPXkbqr2"}];

	// request, respone and next variables to be reset after each test
	let request = {
		body:{username:"OzzyTheGiant", password:"notarealpassword"},
		session:{ regenerate:sinon.stub().callsArg(0) }
	};

	let response = {
		clearCookie:sinon.stub(),
		status:sinon.stub().returnsThis(),
		json:sinon.stub().returnsThis(),
		end:sinon.stub().returns(null)
	};

	const next = sinon.stub();
	const addCSRFToken = sinon.stub();

	// create db dependency
	const db = {
		select:sinon.stub().returnsThis(),
		from:sinon.stub().returnsThis(),
		where:sinon.stub().returns(new Promise(
			resolve => request.body.username == "OzzyTheGiant" ? resolve(data) : resolve(null))
		),
	};

	const controller = new LoginController(db, addCSRFToken);

	process.env.SESSION_COOKIE = "electro";

	function assertAuthenticationErrorThrown(error) {
		assert.equal(db.select.callCount, 1);
		assert.equal(db.from.getCall(0).args[0], LoginController.tableName);
		assert.deepEqual(db.where.getCall(0).args[0], {Username:request.body.username});
		assert.equal(response.clearCookie.getCall(0).args[0], process.env.SESSION_COOKIE);
		assert.isTrue(error instanceof AuthenticationError);
	}

	it("verifies user credentials, starts session and logs in user", () => {
		return controller.login(request, response, next).then(() => {
			assert.equal(db.select.callCount, 1);
			assert.equal(db.from.getCall(0).args[0], LoginController.tableName);
			assert.deepEqual(db.where.getCall(0).args[0], {Username:request.body.username});
			assert.deepEqual(request.session.user, { ID:data[0].ID, Username:data[0].Username });
			assert.equal(response.json.getCall(0).args[0], request.session.user);
		}).catch(error => { throw error; });
	});

	it("throws AuthenticationError if username does not exist", () => {
		return controller.login(request, response, next).catch(assertAuthenticationErrorThrown);
	});

	it("throws AuthenticationError if password is incorrect", () => {
		return controller.login(request, response, next).catch(assertAuthenticationErrorThrown);
	});

	it("regenerates session id and calls csrf middleware when logging out", () => {
		controller.logout(request, response, next);
		assert.equal(request.session.regenerate.callCount, 1);
		assert.equal(addCSRFToken.callCount, 1);
		assert.equal(response.status.getCall(0).args[0], 204);
		assert.equal(response.end.callCount, 1);
	});

	afterEach(() => {
		next.resetHistory();
		for (var key in response) response[key].resetHistory();
		for (var key in db) db[key].resetHistory();
	});
});
