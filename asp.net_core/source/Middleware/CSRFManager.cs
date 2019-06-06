using System;
using System.Threading.Tasks;
using electro.Exceptions;
using Microsoft.AspNetCore.Antiforgery;
using Microsoft.AspNetCore.Http;

namespace electro.Middleware {
	public class CSRFManager {
		private readonly RequestDelegate next;
		private readonly IAntiforgery antiforgery;
		private readonly int cookieLifetime = Int32.Parse(Environment.GetEnvironmentVariable("SESSION_LIFETIME"));

		public CSRFManager(RequestDelegate next, IAntiforgery antiforgery) {
        	this.next = next;
        	this.antiforgery = antiforgery;
    	}

		public async Task InvokeAsync(HttpContext context) {
			var method = context.Request.Method;
			if (HttpMethods.IsPost(method) || HttpMethods.IsPut(method) || HttpMethods.IsDelete(method)) {
				try {
            		await antiforgery.ValidateRequestAsync(context);
				} catch (AntiforgeryValidationException) {
					throw new AuthorizationException();
				}
        	} 
			
			// if route is an action route and token was validated successfully, create new token
			var tokens = antiforgery.GetAndStoreTokens(context);
			context.Response.Cookies.Append(
				Environment.GetEnvironmentVariable("XSRF_COOKIE"), 
				tokens.RequestToken, 
				new CookieOptions() { 
					MaxAge = TimeSpan.FromMinutes(cookieLifetime),
					HttpOnly = false ,
					Secure = Environment.GetEnvironmentVariable("APP_ENV") != "local"
				}
			);

			await next(context);
		}
	}
}