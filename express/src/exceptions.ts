export class ValidationError extends Error {
    static types = {
        required:1,
        isDate:2,
        maxNumberSize:3,
        maxStringSize:4
    }

    public message = "The data submitted was invalid"
    public readonly code = 400
    public readonly loggable = false

	public constructor(fieldName: string, category: number) {
		super()
		switch(category) {
			case ValidationError.types.required: 
                this.message = `${fieldName} is required`; break
			case ValidationError.types.isDate: 
                this.message = `${fieldName} is not a valid date`; break
			case ValidationError.types.maxNumberSize: 
                this.message = `${fieldName} must be between $0 and $99,999.99`; break
			case ValidationError.types.maxStringSize: 
                this.message = `${fieldName} is too large`; break
		}
	}
}

export class NotFoundError extends Error {
    public readonly code = 404
    public readonly loggable = false

	public constructor(entity: string) { // should be "API route" or "record"
		super()
		if (!entity) entity = "item"
		this.message = `The specified ${entity} could not be found`
	}
}

export class DatabaseError extends Error {
    public metadata: { [key: string]: any }
    public readonly code = 500
    public readonly loggable = true

	constructor(hiddenMessage: { [key: string]: any }) {
		super("Error occurred while querying the database");
		this.metadata = { hiddenMessage }
		this.code = 500
	}
}

export class AuthenticationError extends Error {
    public readonly message = "Username or password is not correct"
    public readonly code = 401
    public readonly loggable = false
}

export class AuthorizationError extends Error {
    public readonly message = "You are not authorized to perform this request"
    public readonly code = 403;
    public readonly loggable = false
}

export class EmptyRequestBodyError extends Error {
	public readonly message = "No data was submitted to the server";
	public readonly code = 400;
	public readonly loggable = false;
}
