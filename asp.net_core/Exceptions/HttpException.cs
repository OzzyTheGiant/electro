using Newtonsoft.Json;

namespace electro.Exceptions
{
    public class HttpException {
		[JsonIgnore]
        [JsonProperty(DefaultValueHandling = DefaultValueHandling.Ignore)]
        public int Code { get; private set; }

        public string Message { get; private set; }

		[JsonIgnore]
		public string HiddenMessage { get; protected set; }

        public HttpException() {
            Code = 500;
			Message = "Server Error: Please try again";
        }

        public HttpException(string message) {
            Code = 500;
			Message = message;
        }

        public HttpException(int code, string message) {
            Code = code;
			Message = message;
		}
    }
}