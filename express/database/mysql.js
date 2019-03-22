const knex = require("knex");
const dbConfig = require("../config/db-config");
const logger = require("../services/logger");

module.exports = knex({ // the knex function will output a connection pool;
	...dbConfig,
	afterCreate:(connection, done) => {
		logger.notice("Database connection created", {
			client:dbConfig.client, 
			host:dbConfig.connection.host
		});
		connection.query('SELECT set_limit(0.01);', error => done(error, connection));
	}
});
