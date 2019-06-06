namespace electro.Exceptions {
	public class ValidationException : HttpException {
		public ValidationException(string message) :base(400, message) {}
	}
}