import knex from "knex"
import { Logger } from "winston"

interface ConnectionParams {
    host: string
    port: number
    database: string
    user: string
    password: string
}

export default function createKnexQueryBuilder(
    client: string,
    connection: ConnectionParams,
    logger: Logger
) {
    return knex({ // the knex function will output a connection pool;
        client,
        connection,
        pool: {
            afterCreate: (connection: any, done: CallableFunction) => {
                logger.info("Database connection created", { client, host: connection.host })
                connection.query(
                    'SELECT count(1);',
                    (error: any) => done(error, connection)
                )
            }
        }
    });
}
