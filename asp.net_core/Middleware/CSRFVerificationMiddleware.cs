using System.Threading.Tasks;
using electro.Exceptions;
using Microsoft.AspNetCore.Antiforgery;
using Microsoft.AspNetCore.Http;

namespace electro.Middleware {
	public class CSRFVerificationMiddleware {
		private readonly RequestDelegate next;
		private readonly IAntiforgery antiforgery;

		public CSRFVerificationMiddleware(RequestDelegate next, IAntiforgery antiforgery) {
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
        	} await next(context);
		}
	}
}