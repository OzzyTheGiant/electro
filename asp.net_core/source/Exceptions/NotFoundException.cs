namespace electro.Exceptions {
	public class NotFoundException : HttpException {
		public NotFoundException(string item): base(404, $"The specified {item} could not be found") {}
	}
}