import express, { NextFunction, Request, Response } from "express";
// const csurf = require('csurf');
import BillController from "@app/controllers/bill-controller"
import LoginController from "@app/controllers/login-controller"
import { db, appKey, sessionName, cookieDefaultSettings, sessionLifetime  } from "@app/config"
import { checkIsAuthenticated } from "./middleware/middleware"
// import { addCSRFToken } from "./middleware/middleware";
// import { AuthorizationError } from "@app/exceptions"

// create router and initiate csrf protection middleware module
// const csrfProtection = csurf();
const router = express.Router()

// instantiate controllers with dependencies
const billController = new BillController(db)
const loginController = new LoginController(db, appKey, sessionName, sessionLifetime, cookieDefaultSettings)

// router.get("/", csrfProtection, addCSRFToken, (request, response, next) => {
// 	response.status(204).end();
// })

router.post("/login", loginController.login.bind(loginController));
router.post("/logout", loginController.logout.bind(loginController));

router
    .route("/bills(/:id)?")
    .get(checkIsAuthenticated, billController.getAll.bind(billController))
    .post(checkIsAuthenticated, billController.add.bind(billController))
    .put(checkIsAuthenticated, billController.update.bind(billController))
    .delete(checkIsAuthenticated, billController.delete.bind(billController))

router.use((error: any, request: Request, response: Response, next: NextFunction) => {
	/* catch invalid CSRF errors */
	// if (error.code === 'EBADCSRFTOKEN') {
	// 	return next(new AuthorizationError());
	// }
    return next(error)
})

export default router
