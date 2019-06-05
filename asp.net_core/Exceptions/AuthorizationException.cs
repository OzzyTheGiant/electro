namespace electro.Exceptions {
	public class AuthorizationException : HttpException {
		public AuthorizationException():base(403, "You are not authorized to perform this action") {}
	}
}