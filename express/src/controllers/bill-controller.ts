import { Request, Response, NextFunction } from "express"
import Bill from "@app/models/Bill"
import { DatabaseError, NotFoundError } from "@app/exceptions"
import { Knex } from "knex"
import { User } from "@app/models/User"

export default class BillController {
    static tableName = "bills"

    private db!: Knex

    public constructor(db: Knex) {
        this.db = db
    }

    /** Get all bills from database	*/
    public async getAll(
        request: Request & { user: User }, 
        response: Response, 
        next: NextFunction
    ): Promise<void> {
        try {
            const results = await this.db.select()
                .from(BillController.tableName)
                .where({ user_id: request.user.id })
                .orderBy('payment_date', 'DESC')

            response.json(results)
        } catch (error: any) {
            next(new DatabaseError(error.message))
        }
    }

    /** Insert new bills into database */
    public async add(request: Request, response: Response, next: NextFunction): Promise<void> {
        try {
            const bill = Bill.withValidatedData(request.body)
            const result = await this.db.insert(bill).into(BillController.tableName)
            
            if (!result) return next(new NotFoundError("bill"))
            bill.id = result[0]
            response.status(201).json(bill)
        } catch (error: any) {
            next(new DatabaseError(error.message))
        }
    }

    /** Update bills in database */
    public async update(
        request: Request & { user: User }, 
        response: Response, 
        next: NextFunction
    ): Promise<void> {
        const bill = Bill.withValidatedData(request.body)

        try {
            const result = await this.db(BillController.tableName)
                .where('id', '=', request.params.id)
                .andWhere("user_id", "=", request.user.id)
                .update(bill)
            if (!result) return next(new NotFoundError("bill"))
            response.json(bill)
        } catch (error: any) {
            next(new DatabaseError(error.message))
        }
    }

    /** Remove bill from database */
    public async delete(
        request: Request & { user: User }, 
        response: Response, 
        next: NextFunction
    ): Promise<void> {
        try {
            const result = await this.db(BillController.tableName)
                .where('id', '=', request.params.id)
                .andWhere("user_id", request.user.id)
                .del()

            if (!result) return next(new NotFoundError("bill"))
            response.status(204).end()
        } catch (error: any) {
            next(new DatabaseError(error.message))
        }
    }
}
