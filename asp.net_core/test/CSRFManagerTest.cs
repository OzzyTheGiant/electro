using electro.Middleware;
using Microsoft.AspNetCore.Http;
using Moq;
using NUnit.Framework;
using Microsoft.AspNetCore.Antiforgery;
using System.Threading.Tasks;
using System;

namespace Tests {
	[TestFixture]
	public class CSRFManagerTest {
		private HttpContext context = new DefaultHttpContext();
		private Mock<IAntiforgery> antiforgery = new Mock<IAntiforgery>();
		private AntiforgeryTokenSet tokenSet = new AntiforgeryTokenSet("asdfasdfasdfa", "rqwerqwerqw", "csrf_token", "X-XSRF-TOKEN");
		private Mock<IResponseCookies> cookies = new Mock<IResponseCookies>();
		private CSRFManager csrf;
		private int nextCalls = 0;

		[SetUp]
		public void Setup() {
			Environment.SetEnvironmentVariable("SESSION_LIFETIME", "60");
			Environment.SetEnvironmentVariable("XSRF_COOKIE", "XSRF_TOKEN");
			Environment.SetEnvironmentVariable("APP_ENV", "local");
			antiforgery.Setup(antiforgery => antiforgery.GetAndStoreTokens(context)).Returns(tokenSet);
			csrf = new CSRFManager(async context => await Task.FromResult(nextCalls++), antiforgery.Object);
			cookies
				.Setup(cookies => cookies.Append(It.IsAny<string>(), It.IsAny<string>(), It.IsAny<CookieOptions>()))
				.Callback<string, string, CookieOptions>(verifyCookie);
			context.Request.Method = "POST";
		}

		[Test]
		public async Task CSRFManager_VerifiesToken() {
			antiforgery.Setup(antiforgery => antiforgery.ValidateRequestAsync(context)).Returns(Task.FromResult(true));
			await csrf.InvokeAsync(context);
			antiforgery.Verify(antiforgery => antiforgery.ValidateRequestAsync(context), Times.Once());
			Assert.AreEqual(nextCalls, 1);
			nextCalls = 0;
		}

		private void verifyCookie(string name, string value, CookieOptions options) {
			Assert.AreEqual(name, "XSRF_TOKEN");
			Assert.AreEqual(value, "asdfasdfasdfa");
			Assert.AreEqual(options.MaxAge, TimeSpan.FromMinutes(60));
			Assert.IsFalse(options.HttpOnly);
			Assert.IsFalse(options.Secure);
		}
	}
}