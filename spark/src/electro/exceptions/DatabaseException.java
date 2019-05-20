package electro.exceptions;

public class DatabaseException extends HttpException {
	private static final long serialVersionUID = 1L;
	private String hiddenMessage;

	public DatabaseException(String hiddenMessage) {
		super("Something went wrong while querying the database");
		this.hiddenMessage = hiddenMessage;
	}

	public String getHiddenMessage() {
		return hiddenMessage;
	}
}