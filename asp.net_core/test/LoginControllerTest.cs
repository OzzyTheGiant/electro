using System.Collections.Generic;
using System.Linq;
using electro.Controllers;
using electro.Database;
using electro.Models;
using electro.Extensions;
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using Moq;
using NUnit.Framework;
using Tests.Mocks;
using Newtonsoft.Json;
using System.Text;
using System;

namespace Tests {
	[TestFixture]
	public class LoginControllerTest {
		private IQueryable data = new List<User>() {
			new User() { Username = "OzzyTheGiant", Password = "$2a$10$Cj66BNdUZhkMvStI5jfQoetgzSvkaQIwJuIRDPIa1zgFsFPXkbqr2" }
		}.AsQueryable();

		private Mock<DbSet<User>> mockDbSet;
		private Mock<UserContext> mockDbContext;
		private LoginController controller;
		private Mock<ISession> session;

		[SetUp]
        public void Setup() {
			// create mock data source
			mockDbSet = new Mock<DbSet<User>>();
			mockDbSet.As<IQueryable<User>>().Setup(set => set.Provider).Returns(new TestDbAsyncQueryProvider<User>(data.Provider));
			mockDbSet.As<IQueryable<User>>().Setup(set => set.Expression).Returns(data.Expression);
			mockDbSet.As<IQueryable<User>>().Setup(set => set.ElementType).Returns(data.ElementType);
			mockDbSet.As<IQueryable<User>>().Setup(set => set.GetEnumerator()).Returns((IEnumerator<User>) data.GetEnumerator());
			mockDbSet.As<IAsyncEnumerable<User>>().Setup(set => set.GetEnumerator()).Returns(new TestDbAsyncEnumerator<User>((IEnumerator<User>) data.GetEnumerator()));
			// create mock db context
			mockDbContext = new Mock<UserContext>();
			mockDbContext.Setup(context => context.Users).Returns(mockDbSet.Object);
			// create mock Session
			session = new Mock<ISession>();
			// create controller with mocks for DbContext and HttpContext with mock Session
			controller = new LoginController(mockDbContext.Object);
			controller.ControllerContext = new ControllerContext();
			controller.ControllerContext.HttpContext = new DefaultHttpContext();
			controller.HttpContext.Session = session.Object;
        }

		[Test]
        public void Login_VerifiesCredentialsAndStartsSession() {
			var credentials = new User() { Username = "OzzyTheGiant", Password = "notarealpassword" };
            var result = controller.Login(credentials);
			Assert.IsInstanceOf<ActionResult<User>>(result);
			Assert.AreEqual(credentials.Username, result.Value.Username);
        }

		[Test]
        public void Login_ThrowsAuthenticationExceptionIfCredentialsNotValid([Values("ozzy", "OzzyTheGiant")] string username) {
			var credentials = new User() { Username = username, Password = username };
			var result = controller.Login(credentials);
			Assert.IsInstanceOf<ActionResult<User>>(result);
			Assert.IsInstanceOf<UnauthorizedObjectResult>(result.Result);
        }

		[Test]
		public void Logout_ClearsSessionAndDeletesSessionCookie() {
			controller.sessionCookieName = "electro";
			var result = controller.Logout();
			Assert.IsInstanceOf<NoContentResult>(result);
			session.Verify(session => session.Clear(), Times.Once());
		}
	}
}