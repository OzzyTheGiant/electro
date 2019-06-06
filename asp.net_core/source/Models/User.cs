using System.ComponentModel.DataAnnotations.Schema;
using Newtonsoft.Json;

namespace electro.Models {
	public class User {
		public int Id { get; set; }

		[Column(TypeName = "VARCHAR(30)")]
		public string Username { get; set; }

		[JsonProperty(NullValueHandling = NullValueHandling.Ignore)]
		[Column (TypeName = "VARCHAR(255)")]
		public string Password { get; set; }
	}
}