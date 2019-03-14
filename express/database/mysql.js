const knex = require("knex");
const dbConfig = require("../config/db-config");

module.exports = knex({ // the knex function will output a connection pool;
	...dbConfig,
	afterCreate:(connection, done) => {
		console.log("connection created");
		connection.query('SELECT set_limit(0.01);', error => done(error, connection));
	}
});
