namespace electro.Exceptions {
	public class AuthenticationException : HttpException {
		public AuthenticationException():base(401, "Username or password is incorrect") {}
	}
}