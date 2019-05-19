package electro.exceptions;

public class NotFoundException extends HttpException {
	private static final long serialVersionUID = 1L;
	private static final String MESSAGE = "The specified %1$s could not be found";

	public NotFoundException(String item) {
		super(String.format(MESSAGE, item == null ? "item" : item), 404);
	}
}