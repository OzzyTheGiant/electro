import sinon from "sinon"
import { assert } from "chai"
import LoginController from "@app/controllers/login-controller"
import { AuthenticationError } from "@app/exceptions"
import { Request } from "express"
import { User } from "@app/models/User"

describe("Login Controller", () => {
    const password = "$argon2id$v=19$m=65536,t=3,p=4$6ZUnl18cx8y6EpHuT4dOcg$wxzAQ+TApVsqUZmV9o+WojF/6ZZ2bUEZWDNKe6Jzgrw"
    const data = [{ 
        id: 1, 
        username: "OzzyTheGiant", 
        password
    }]

	// request, response and next variables to be reset after each test
	let request = {
		body: { username: "OzzyTheGiant", password: "notarealpassword" },
        params: {},
        user: {}
	} as Request & { user: User }

	let response = {
        cookie: sinon.stub().returnsThis(),
		clearCookie: sinon.stub().returnsThis(),
		status: sinon.stub().returnsThis(),
		json: sinon.stub().returnsThis(),
		end: sinon.stub().returns(null)
	} as any

    const cookieOptions = {
        httpOnly: true,
        secure: false
    }

	const next = sinon.stub()

	// create db dependency
	const db = {
		select: sinon.stub().returnsThis(),
		from: sinon.stub().returnsThis(),
		where: sinon.stub().resolves(data)
	} as any

	const controller = new LoginController(db, "sample_key", "electro", 30, cookieOptions)

	function assertAuthenticationErrorThrown() {
		assert.equal(db.select.callCount, 1)
		assert.equal(db.from.getCall(0).args[0], LoginController.tableName)
		assert.deepEqual(db.where.getCall(0).args[0], { username: request.body.username })
		assert.isTrue(next.getCall(0).args[0] instanceof AuthenticationError)
	}

	it("verifies user credentials, sets JWT and logs in user", async () => {
		await controller.login(request, response, next)
        assert.equal(db.select.callCount, 1)
        assert.equal(db.from.getCall(0).args[0], LoginController.tableName)
        assert.deepEqual(db.where.getCall(0).args[0], { username: request.body.username })
        assert.equal(response.cookie.getCall(0).args[0], "electro")
        assert.deepEqual(response.json.getCall(0).args[0], { 
            id: data[0].id, 
            username: data[0].username 
        } as any)
	})

    it("throws AuthenticationError if username does not exist", async () => {
        db.where.resolves([])
        data[0].password = password
        await controller.login(request, response, next)
        assertAuthenticationErrorThrown()
	})
    
	it("throws AuthenticationError if password is incorrect", async () => {
        request.body.password = "fake_password"
        data[0].password = password
        await controller.login(request, response, next)
        assertAuthenticationErrorThrown()
	})

	it("clears jwt cookie on logging out", () => {
		controller.logout(request, response)
		assert.equal(response.status.getCall(0).args[0], 204)
		assert.equal(response.clearCookie.callCount, 1)
		assert.equal(response.end.callCount, 1)
	})

	afterEach(() => {
		next.resetHistory()
		for (var key in response) response[key].resetHistory()
		for (var key in db) db[key].resetHistory()
	})
})
