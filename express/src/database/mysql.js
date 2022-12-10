const knex = require("knex");
const dbConfig = require("../config/db-config");

module.exports = function createKnexQueryBuilder(client, host, port, database, user, password, logger) {
	return knex({ // the knex function will output a connection pool;
		client,
		connection:{ 
			host, 
			port, 
			database, 
			user: dbConfig.user || user, 
			password: dbConfig.password || password 
		},
		afterCreate:(connection, done) => {
			logger.notice("Database connection created", { client, host });
			connection.query('SELECT set_limit(0.01);', error => done(error, connection));
		}
	});
}
