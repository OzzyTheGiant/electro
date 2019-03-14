class ValidationError extends Error {
	constructor(fieldName, category) {
		super();
		this.message = "The data submitted was invalid";
		this.code = 400;

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
	}
}

class DatabaseError extends Error {
	constructor(hiddenMessage) {
		super("Error occurred while querying the database");
		this.hiddenMessage = hiddenMessage;
		this.code = 500;
	}
}

module.exports = { ValidationError, NotFoundError, DatabaseError };
