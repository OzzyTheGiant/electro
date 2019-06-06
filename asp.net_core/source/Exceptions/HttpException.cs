using System;
using Newtonsoft.Json;

namespace electro.Exceptions {
	[JsonObject(MemberSerialization.OptIn)]
    public class HttpException : Exception {
		public static readonly string defaultMessage = "Server Error: Please try again";

        public int Code { get; private set; }

		[JsonProperty(PropertyName = "message")]
        public override string Message { get; }

		public string HiddenMessage { get; protected set; }

        public HttpException():base(defaultMessage) {
			Message = defaultMessage;
            Code = 500;
        }

        public HttpException(string message):base(message) {
			Message = message;
            Code = 500;
        }

        public HttpException(int code, string message):base(message) {
			Message = message;
            Code = code;
		}
    }
}