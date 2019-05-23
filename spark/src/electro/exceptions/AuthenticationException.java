package electro.exceptions;

public class AuthenticationException extends HttpException {
	private static final long serialVersionUID = 1L;

	public AuthenticationException() {
		super("Username or password is incorrect", 401);
	}
}