import path from "path"
import winston from "winston"

export default function createWinstonLogger(appEnv: string, logFilePath: string) {
    const logger = winston.createLogger({
        level: "info",
        format: winston.format.json(),
        transports: [
            new winston.transports.File({ filename: 'error.log', level: 'error' }),
        ],
    });

    // If we're not in production then log to the `console` with the format:
    // `${info.level}: ${info.message} JSON.stringify({ ...rest }) `
    if (appEnv !== "production") {
        logger.add(new winston.transports.Console({
            format: winston.format.combine()
        }))
    }

    return logger
};
