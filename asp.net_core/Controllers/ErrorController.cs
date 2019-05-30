using System;
using System.Net;
using electro.Exceptions;
using Microsoft.AspNetCore.Mvc;
using Microsoft.AspNetCore.Mvc.ModelBinding;

namespace electro.Controllers {
	[Route("/errors")]
	[ApiController]
	public class ErrorController : ControllerBase {
		public static Func<ActionContext, IActionResult> validationResponseFactory = (context) => {
			/* Put all error messages for multiple model fields into one string, under the json key "message" */
			string message = "";
			foreach (ModelStateEntry entry in context.ModelState.Values) {
				foreach(ModelError error in entry.Errors) {
					message += error.ErrorMessage + " ";
				}
			}
			return new BadRequestObjectResult(new ValidationException(message));
		};

		[Route("{code}")]
		public IActionResult Error(int code) {
			HttpException error;
            switch(code) {
				case 400: error = new ValidationException("The data provided is invalid"); break;
				default: error = new HttpException(); break;
			} return new ObjectResult(error);
        }
	}
}