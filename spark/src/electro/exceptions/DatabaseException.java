package electro.exceptions;

public class DatabaseException extends HttpException implements Loggable {
	private static final long serialVersionUID = 1L;

	public DatabaseException(String hiddenMessage) {
		super("Something went wrong while querying the database", hiddenMessage);
	}
}