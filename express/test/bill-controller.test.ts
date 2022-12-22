import sinon from "sinon"
import { assert } from "chai"
import { Request } from "express" 
import BillController from "@app/controllers/bill-controller"
import { NotFoundError } from "@app/exceptions"
import { User } from "@app/models/User"

describe("Bill Controller", () => {
    const data = [
        { "ID": 1, "User": 1, "PaymentAmount": 80.08, "PaymentDate": "2019-06-12" },
        { "ID": 2, "User": 1, "PaymentAmount": 81.08, "PaymentDate": "2019-06-13" }
    ]

    // request, response and next variables to be reset after each test
    const request = {
        body: { "user_id": 1, "payment_amount": 91.08, "payment_date": "2019-06-14" },
        params: {},
        user: { id: 1, username: "OzzyTheGiant", password: "" }
    } as Request & { user: User }

    const response = {
        status: sinon.stub().returnsThis(),
        json: sinon.stub().returnsThis(),
        end: sinon.stub().returns(null)
    } as any

    const next = sinon.stub()

    // db dependency
    const db = sinon.stub() as any
    db.returns(db)

    const dbMethods = {
        select: sinon.stub().returnsThis(),
        from: sinon.stub().returnsThis(),
        orderBy: sinon.stub().resolves(data),
        insert: sinon.stub().returnsThis(),
        into: sinon.stub().resolves([3]),
        where: sinon.stub().returnsThis(),
        andWhere: sinon.stub().returnsThis(),
        update: sinon.stub(),
        del: sinon.stub()
    }

    // assign all database method stubs to mock database
    Object.assign(db, dbMethods)

    const billController = new BillController(db)

    it("fetches all bills from database", async () => {
        await billController.getAll(request, response, next)
        assert.equal(db.select.callCount, 1)
        assert.equal(db.from.getCall(0).args[0], BillController.tableName)
        assert.deepEqual(db.orderBy.getCall(0).args, ['payment_date', 'DESC'])
        assert.equal(response.json.getCall(0).args[0], data)
    })

    it("adds new bill to database", async () => {
        await billController.add(request, response, next)
        assert.equal(db.insert.callCount, 1)
        assert.equal(db.into.getCall(0).args[0], BillController.tableName)
        assert.equal(response.status.getCall(0).args[0], 201)
        assert.deepEqual(response.json.getCall(0).args[0], { ...request.body, id: 3 })
    })

    it("updates bill in database", async () => {
        request.body.id = 3
        request.params.id = "3"
        db.update.resolves(request.params.id === "4" ? "null" : 1)

        await billController.update(request, response, next)
        assert.equal(db.getCall(0).args[0], BillController.tableName)
        assert.deepEqual(db.where.getCall(0).args, ["id", "=", request.params.id])
        assert.deepEqual(db.update.getCall(0).args[0], request.body)
        assert.deepEqual(response.json.getCall(0).args[0], request.body)
    })

    it("sends NotFoundException if no bill found when updating a bill", async () => {
        request.body.id = 4
        request.params.id = "4"
        db.update.resolves(request.params.id === "4" ? null : 1)

        await billController.update(request, response, next)
        assert.equal(db.getCall(0).args[0], BillController.tableName)
        assert.deepEqual(db.where.getCall(0).args, ["id", "=", request.params.id])
        assert.deepEqual(db.update.getCall(0).args[0], request.body)
        assert.isTrue(next.getCall(0).args[0] instanceof NotFoundError)
    })

    it("deletes bill from database", async () => {
        request.body.id = 3
        request.params.id = "3"
        db.del.resolves(request.params.id !== "3" ? null : 1)

        await billController.delete(request, response, next)
        assert.equal(db.getCall(0).args[0], BillController.tableName)
        assert.deepEqual(db.where.getCall(0).args, ["id", "=", request.params.id])
        assert.equal(db.del.callCount, 1)
        assert.deepEqual(response.status.getCall(0).args[0], 204)
        assert.equal(response.end.callCount, 1)
    })

    it("sends NotFoundException if no bill found when deleting a bill", async () => {
        request.body.id = 4
        request.params.id = "4"
        db.del.resolves(request.params.id === "4" ? null : 1)

        await billController.delete(request, response, next)
        assert.equal(db.getCall(0).args[0], BillController.tableName)
        assert.deepEqual(db.where.getCall(0).args, ["id", "=", request.params.id])
        assert.equal(db.del.callCount, 1)
        assert.isTrue(next.getCall(0).args[0] instanceof NotFoundError)
    })

    afterEach(() => {
        db.resetHistory()
        next.resetHistory()
        for (var key in response) response[key].resetHistory()
        for (var key in dbMethods) (dbMethods as any)[key].resetHistory()
    })
})