using Microsoft.AspNetCore.Builder;

namespace electro.Middleware {
	public static class ApplicationBuilderExtensions {
		public static IApplicationBuilder UseCSRFVerificationMiddleware(this IApplicationBuilder app) {
			return app.UseMiddleware<CSRFVerificationMiddleware>();
		}

		public static IApplicationBuilder UseGlobalExceptionHandler(this IApplicationBuilder app) {
			return app.UseMiddleware<GlobalExceptionHandler>();
		}
	}
}