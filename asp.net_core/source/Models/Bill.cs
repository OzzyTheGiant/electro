using System;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace electro.Models {
	public class Bill {
		public int Id { get; set; }
		
		// Note: nullable types (int?, etc.) are to prevent default values from
		// being used if the values are not present in json request body, enforcing the
		// required attribute validation
		[Required(ErrorMessage = "User is required")]
		public int? User { get; set; }

		[Required(ErrorMessage = "Payment amount is required")]
		[DataType(DataType.Currency)]
		[Column(TypeName = "decimal(5, 2)")]
		[Range(0.01, 99999.99)]
		public decimal PaymentAmount { get; set; }

		[Required(ErrorMessage = "Payment date is required")]
		[DataType(DataType.Date, ErrorMessage = "Payment date is invalid")]
		public DateTime? PaymentDate { get; set; }
	}
}