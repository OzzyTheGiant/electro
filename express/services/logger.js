const path = require('path');
const consoleLogger = require('bristol');
const palin = require('palin');

const levels = [
	"error",
	"warning",
	"notice",
	"info",
	"debug"
];

module.exports = function createBristolLogger(appEnv, logFilePath) {
	const logger = { transports: [ consoleLogger ] };
	let fileLogger = null;

	consoleLogger.setSeverities(levels);
	consoleLogger.addTarget('console')
		.withLowestSeverity('debug')
		.withFormatter(appEnv !== 'cloud' ? palin : 'json'); // palin is same as 'human' format but with colors

	if (appEnv !== 'cloud') {
		/* create file logger and push to logger transports array */
		fileLogger = new consoleLogger.Bristol();
		logger.transports.push(fileLogger);
		fileLogger.setSeverities(levels);
		fileLogger.addTarget('file', {file:logFilePath || path.join(process.cwd(), "logs/application.log")})
			.withLowestSeverity('info')
			.withFormatter('human');
	}

	for (const level of levels) { // set the log level methods for the main logger object
		logger[level] = (message, metadata) => {
			for (const transport of logger.transports) {
				transport[level](message, metadata); // call the log level method for each transport
			}
		};
	}
	
	return logger;
};
