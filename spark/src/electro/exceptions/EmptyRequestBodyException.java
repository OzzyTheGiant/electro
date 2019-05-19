package electro.exceptions;

public class EmptyRequestBodyException extends HttpException {
	private static final long serialVersionUID = 1L;

	public EmptyRequestBodyException() {
		super("No data was provided", 400);
	}
}