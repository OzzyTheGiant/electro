using Microsoft.EntityFrameworkCore;
using electro.Models;
	
namespace electro.Database {
	public class BillContext : DbContext {
		public BillContext (DbContextOptions<BillContext> options) : base (options) { }
		public DbSet<Bill> Bills { get; set; }
	}
}