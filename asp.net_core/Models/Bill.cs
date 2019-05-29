using System;

namespace electro.Models {
	public class Bill {
		public int Id { get; set; }
		public int User { get; set; }
		public decimal PaymentAmount { get; set; }
		public DateTime PaymentDate { get; set; }
	}
}