import createExpressApp, { Request, Response, NextFunction } from "express"
import cookieParser from "cookie-parser"
import bodyParser from "body-parser"
import router from "@app/routes"
import { logger } from "@app/config"
import { NotFoundError } from "@app/exceptions"

const app = createExpressApp()

/* Log server shut down events */
process.on("SIGINT", () => {
    logger.info("\nServer is shutting down manually")
    process.exit(0)
})

process.on("exit", code => logger.info(`Server is shutting down with code ${code}`))
process.on("uncaughtException", error => {
    logger.error(error)
    process.exit(1)
})

/* Middlewares and Router */
app.use(bodyParser.json())
app.use(cookieParser())
app.use("/api", router)

/* Error 404 - Not Found handler */
app.use((_: Request, __: Response, next: NextFunction) => {
    next(new NotFoundError("API Route"))
})

/* Global error handler */
app.use((error: any, _: Request, response: Response, __: NextFunction) => {
    if (error.loggable) logger.error(error.message, error.metadata)
    response.status(error.code || 500).json({ message: error.message })
})

/* Start server */
app.listen(process.env.APP_PORT || 3000, function () {
    logger.info("Server listening on port " + process.env.APP_PORT)
})
