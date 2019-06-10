using Microsoft.EntityFrameworkCore;
using electro.Models;
	
namespace electro.Database {
	public class BillContext : DbContext {
		public BillContext () {}
		public BillContext (DbContextOptions<BillContext> options) : base (options) { }
		public virtual DbSet<Bill> Bills { get; set; }
	}
}