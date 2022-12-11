import winston, { Logger } from "winston"
import { MESSAGE } from "triple-beam"

export default function createWinstonLogger(appEnv: string, logFilePath: string): Logger {
    type TransformableInfo = winston.Logform.TransformableInfo

    const logger = winston.createLogger({
        level: "info",
        format: winston.format.json(),
        transports: [
            new winston.transports.File({ filename: logFilePath, level: 'error' }),
        ],
    });

    // If we're not in production then log to the `console` with the format:
    // `${info.level}: ${info.message} JSON.stringify({ ...rest }) `
    if (appEnv !== "production") {
        logger.add(new winston.transports.Console({
            format: winston.format.combine(
                winston.format.colorize(),
                winston.format.timestamp(),
                winston.format((info: TransformableInfo, _: any): TransformableInfo => {
                    const { level, timestamp, message, ...rest } = info
                    const meta = Object.keys(rest).length ? JSON.stringify(rest) : '';
                    
                    (info as any)[MESSAGE] = `[${timestamp}] ${level}: ${message} ${meta}`
                    return info
                })()
            )
        }))
    }

    return logger
};
