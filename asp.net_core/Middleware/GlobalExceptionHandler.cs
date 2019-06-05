using System;
using System.Threading.Tasks;
using electro.Exceptions;
using Microsoft.AspNetCore.Diagnostics;
using Microsoft.AspNetCore.Http;
using Newtonsoft.Json;

namespace electro.Middleware {
	public class GlobalExceptionHandler {
		private readonly RequestDelegate next;

		public GlobalExceptionHandler(RequestDelegate next) {
			this.next = next;
		}
		
		public async Task InvokeAsync(HttpContext context) {
			try {
				await next(context);
			} catch (HttpException e) {
				await createErrorMessage(e, e.Code, context);
			} catch (Exception) {
				// TODO: log exception from object e
				var httpException = new HttpException();
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