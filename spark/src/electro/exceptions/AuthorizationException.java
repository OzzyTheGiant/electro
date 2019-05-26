package electro.exceptions;

public class AuthorizationException extends HttpException {
	private static final long serialVersionUID = 1L;

	public AuthorizationException() {
		super("You are not authorized to perform this action", 403);
	}
}