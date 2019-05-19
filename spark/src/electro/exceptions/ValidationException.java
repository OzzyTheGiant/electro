package electro.exceptions;

public class ValidationException extends HttpException {
	private static final long serialVersionUID = 1L;

	protected int code = 400;

	public ValidationException(String message) {
		super(message, 400);
	}
}