import { NextFunction, Request, Response } from "express"
import { Knex } from "knex"
import argon2 from "argon2"
import jwt from "jsonwebtoken"
import { User } from "@app/models/User"
import { AuthenticationError } from "@app/exceptions"

export default class LoginController {
    static tableName = "users"

    public readonly appKey: string
    public readonly db!: Knex
    public readonly sessionName!: string
    public readonly sessionLifetime!: number
    public readonly cookieOptions!: { [key: string]: any }

    constructor(
        db: Knex,
        appKey: string,
        sessionName: string,
        sessionLifetime: number,
        cookieOptions: { [key: string]: any }
    ) {
        this.db = db
        this.appKey = appKey
        this.sessionName = sessionName
        this.sessionLifetime = sessionLifetime
        this.cookieOptions = cookieOptions
    }

    public async login(request: Request, response: Response, next: NextFunction): Promise<void> {
        const result = await this.db.select()
            .from(LoginController.tableName)
            .where({ username: request.body.username })

        const user: User = result[0]
        let confirmed = false

        if (user) confirmed = await argon2.verify(user.password, request.body.password)
        if (!confirmed || !user) next(new AuthenticationError())

        delete user.password
        const token = jwt.sign({ data: user }, this.appKey, { 
            expiresIn: `${this.sessionLifetime}min`
        })

        response = response.cookie(this.sessionName, token, this.cookieOptions)
        response.json(user)
    }

    public async logout(request: Request, response: Response): Promise<void> {
        response.clearCookie(this.sessionName).end()
    }
}

module.exports = LoginController
