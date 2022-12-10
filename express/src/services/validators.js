const { ValidationError } = require("../exceptions/exceptions");

class Validators {
	static required(key, value) {
		if ((typeof value === "string" && value === "") || value === undefined) {
			throw new ValidationError(key, ValidationError.types.required);
		} return true;
	}

	static max(maxLength) {
		return (key, value) => {
			if (typeof value === "string" && value.length > maxLength) {
				throw new ValidationError(key, ValidationError.types.maxStringSize);
			} else if (typeof value === "number" && value > maxLength) {
				throw new ValidationError(key, ValidationError.types.maxNumberSize);
			} return true;
		};
	}

	static isDate(key, value) {
		if (new Date(value).toString() === "Invalid Date") {
			throw new ValidationError(key, ValidationError.types.isDate);
		} return true;
	}
}

module.exports = Validators;
