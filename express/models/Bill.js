const Validators = require("../services/validators");

class Bill {
	constructor(props) {
		this.ID = props.ID;
		this.User = props.User;
		this.PaymentAmount = props.PaymentAmount;
		this.PaymentDate = props.PaymentDate;
	}

	static getValidationHandlers() {
		return {
			User: [Validators.required],
			PaymentAmount: [Validators.required, Validators.max(99999.99)],
			PaymentDate: [Validators.required, Validators.isDate]
		}
	}

	static withValidatedData(data) {
		const properties = Bill.getValidationHandlers();
		for (const prop in properties) {
			for (const validator of properties[prop]) { // validate prop using the specified validators
				if (typeof data[prop] === "string") data[prop] = data[prop].trim(); // trim whitespace
				validator(prop, data[prop]);
			}
		} return new Bill(data);
	}
}

module.exports = Bill;
