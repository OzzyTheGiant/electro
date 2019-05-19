package electro.services;

import javax.validation.ConstraintViolation;
import javax.validation.Validation;
import javax.validation.Validator;

public class ValidationService {
	private Validator validator = Validation.buildDefaultValidatorFactory().getValidator();

	public <T> String validate(T model) {
		var errorMessage = "";
		var validationErrors = validator.validate(model);
		if (!validationErrors.isEmpty()) {
			for (ConstraintViolation<T> error : validationErrors) {
				errorMessage += error.getMessage() + ". ";
			}
		} return errorMessage;
	}
}