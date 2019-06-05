using System;
using System.Linq;
using electro.Database;
using Microsoft.AspNetCore.Mvc;
using BCrypt.Net;
using System.Threading.Tasks;
using electro.Models;
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Http.Extensions;
using electro.Extensions;
using electro.Exceptions;

namespace electro.Controllers {
	[ApiController]
	[Route("api")]
	public class LoginController : ControllerBase {
		private UserContext dbContext;
		private const string userKey = "current_user";

		private string sessionCookieName = Environment.GetEnvironmentVariable("SESSION_COOKIE");

		public LoginController(UserContext context) {
			dbContext = context;
		}

		[HttpGet]
		public IActionResult Home() {
			return NoContent();
		}

		[HttpPost("login")]
		public ActionResult<User> Login([FromBody] User credentials) {
			try {
				var user = dbContext.Users.Where(u => u.Username == credentials.Username).First();
				if (BCrypt.Net.BCrypt.Verify(credentials.Password, user.Password)) {
					user.Password = null;
					// set user object to session using custom session extension method
					HttpContext.Session.Set<User>(userKey, user);
					return user;
				} return new UnauthorizedObjectResult(new AuthenticationException());
			} catch (ArgumentNullException) {
				return new UnauthorizedObjectResult(new AuthenticationException());
			}
		}

		[HttpPost("logout")]
		public ActionResult Logout() {
			HttpContext.Session.Clear();
			Response.Cookies.Delete(Environment.GetEnvironmentVariable("SESSION_COOKIE"));
			return NoContent();
		}
	}
}