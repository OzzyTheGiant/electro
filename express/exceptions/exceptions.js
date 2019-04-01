class ValidationError extends Error {
	constructor(fieldName, category) {
		super();
		this.message = "The data submitted was invalid";
		this.code = 400;
		this.loggable = false;

		switch(category) {
			case ValidationError.types.required: this.message = `${fieldName} is required`; break;
			case ValidationError.types.isDate: this.message = `${fieldName} is not a valid date`; break;
			case ValidationError.types.maxNumberSize: this.message = `${fieldName} must be between $0 and $99,999.99`; break;
			case ValidationError.types.maxStringSize: this.message = `${fieldName} is too large`; break;
		}
	}
}

ValidationError.types = {
	required:1,
	isDate:2,
	maxNumberSize:3,
	maxStringSize:4
};

class NotFoundError extends Error {
	constructor(entity) { // should be "API route" or "record"
		super();
		if (!entity) entity = "item";
		this.message = `The specified ${entity} could not be found`;
		this.code = 404;
		this.loggable = false;
	}
}

class DatabaseError extends Error {
	constructor(hiddenMessage) {
		super("Error occurred while querying the database");
		this.metadata = { hiddenMessage };
		this.code = 500;
		this.loggable = true
	}
}

class AuthenticationError extends Error {
	constructor() {
		super();
		this.message = "Username or password is not correct";
		this.code = 401;
		this.loggable = false
	}
}

class AuthorizationError extends Error {
	constructor() {
		super();
		this.message = "You are not authorized to perform this request";
		this.code = 403;
		this.loggable = false
	}
}

class EmptyRequestBodyError extends Error {
	constructor() {
		super();
		this.message = "No data was submitted to the server";
		this.code = 400;
		this.loggable = false;
	}
}

module.exports = { 
	ValidationError, 
	NotFoundError, 
	DatabaseError, 
	AuthenticationError,
	AuthorizationError,
	EmptyRequestBodyError
};
