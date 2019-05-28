package electro.exceptions;

import java.io.IOException;

public class HttpException extends IOException {
	private static final long serialVersionUID = 1L;
	private static String defaultMessage = "Server Error: Please try again";
	protected int code = 500;
	protected boolean loggable = false;
	protected String hiddenMessage = null;

	public HttpException() {
		super(defaultMessage);
	}

	public HttpException(String message) {
		super(message);
	}

	public HttpException(String message, String hiddenMessage) {
		super(message);
		this.hiddenMessage = hiddenMessage;
	}

	public HttpException(String message, int code) {
		super(message);
		this.code = code;
	}

	public int getStatusCode() {
		return code;
	}

	public String getHiddenMessage() {
		return hiddenMessage;
	}
}