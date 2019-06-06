using System;
using System.Threading.Tasks;
using electro.Exceptions;
using Microsoft.AspNetCore.Diagnostics;
using Microsoft.AspNetCore.Http;
using Microsoft.Extensions.Logging;
using Newtonsoft.Json;

namespace electro.Middleware {
	public class GlobalExceptionHandler {
		private readonly RequestDelegate next;
		private readonly ILogger logger;

		public GlobalExceptionHandler(RequestDelegate next, ILogger<GlobalExceptionHandler> logger) {
			this.next = next;
			this.logger = logger;
		}
		
		public async Task InvokeAsync(HttpContext context) {
			try {
				await next(context);
			} catch (HttpException e) {
				await createErrorMessage(e, e.Code, context);
			} catch (Exception e) {
				var httpException = new HttpException();
				logger.LogError(500, e, e.Message);
				await createErrorMessage(httpException, httpException.Code, context);
			}
		}

		public async Task createErrorMessage(HttpException e, int code, HttpContext context) {
			context.Response.StatusCode = code;
			context.Response.ContentType = "application/json";
			await context.Response.WriteAsync(JsonConvert.SerializeObject(e));
		}
	}
}