import { Router, NextFunction, Request, Response } from "express"
import csurf from "csurf"
import BillController from "@app/controllers/bill-controller"
import LoginController from "@app/controllers/login-controller"
import { db, appKey, sessionName, cookieDefaultSettings } from "@app/config"
import { sessionLifetime, csrfConfig } from "@app/config"
import { checkIsAuthenticated } from "@app/middleware"
import { AuthorizationError } from "@app/exceptions"

// create router and initiate csrf protection middleware module
const csrfProtection = csurf({ cookie: csrfConfig.cookie })
const router = Router()

// instantiate controllers with dependencies
const billController = new BillController(db)
const loginController = new LoginController(db, appKey, sessionName, sessionLifetime, cookieDefaultSettings)

router.get("/", csrfProtection, (request: Request, response: Response) => {
    response.cookie(csrfConfig.name, request.csrfToken(), {
        ...csrfConfig.cookie,
        httpOnly: false
    })

	response.status(204).end()
})

router.post("/login", csrfProtection, loginController.login.bind(loginController));
router.post("/logout", loginController.logout.bind(loginController));

router
    .route("/bills(/:id)?")
    .get(checkIsAuthenticated, billController.getAll.bind(billController))
    .post(checkIsAuthenticated, billController.add.bind(billController))
    .put(checkIsAuthenticated, billController.update.bind(billController))
    .delete(checkIsAuthenticated, billController.delete.bind(billController))

router.use((error: any, _: Request, __: Response, next: NextFunction) => {
	/* catch invalid CSRF errors */
	if (error.code === 'EBADCSRFTOKEN') return next(new AuthorizationError())
    return next(error)
})

export default router
