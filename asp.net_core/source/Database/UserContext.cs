using electro.Models;
using Microsoft.EntityFrameworkCore;

namespace electro.Database {
	public class UserContext : DbContext {
		public UserContext (DbContextOptions<UserContext> options) : base (options) { }
		public DbSet<User> Users { get; set; }
	}
}