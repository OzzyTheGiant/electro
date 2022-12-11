import { NextFunction, Request, Response } from "express"
import jwt from "jsonwebtoken"
import LoginController from "@app/controllers/login-controller"
import { sessionName, appKey, db } from "@app/config"
import { AuthorizationError, DatabaseError } from "@app/exceptions"
import { User } from "@app/models/User"

export async function checkIsAuthenticated(
    request: Request, 
    _: Response, 
    next: NextFunction
): Promise<void> {
    const token = request.cookies[sessionName]
    let user: User
    let jwtObject: any

    try {
        jwtObject = jwt.verify(token, appKey) as any
    } catch (error: any) {
        next(new AuthorizationError())
    }

    try {
        user = (await db.select().from(LoginController.tableName).where({ id: jwtObject.id }))[0]
    } catch (error: any) {
        next(new DatabaseError(error.message))
    }
    
    (request as any).user = user
    if (!user) next(new AuthorizationError())
    return next()
}

// export function deleteSessionCookie(request: Request, response: Response, next: NextFunction): void {
//     if (request.cookies[sessionConfig.name] && !request.session.user) {
//         response.clearCookie(sessionConfig.name)
//     } next()
// };

// export function addCSRFToken(request: Request, response: Response, next: NextFunction): void {
//     response.cookie(csrfConfig.name, request.csrfToken(), csrfConfig.cookie)
//     if (next) next()
// }
