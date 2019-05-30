using Newtonsoft.Json;

namespace electro.Exceptions
{
    public class HttpException {
        [JsonProperty(DefaultValueHandling = DefaultValueHandling.Ignore)]
        public int Code { get; private set; }

        public string Message { get; private set; }

        public HttpException() {
            Code = 500;
			Message = "Server Error: Please try again";
        }

        public HttpException(int code, string message) {
            Code = code;
			Message = message;
		}
    }
}