using Microsoft.AspNetCore.Builder;

namespace electro.Middleware {
	public static class ApplicationBuilderExtensions {
		public static IApplicationBuilder UseCSRFManager(this IApplicationBuilder app) {
			return app.UseMiddleware<CSRFManager>();
		}

		public static IApplicationBuilder UseGlobalExceptionHandler(this IApplicationBuilder app) {
			return app.UseMiddleware<GlobalExceptionHandler>();
		}
	}
}