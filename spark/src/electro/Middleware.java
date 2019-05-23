package electro;

import spark.Filter;

public class Middleware {
	public static Filter setContentType = (request, response) -> {
		if (request.requestMethod() != "DELETE") response.type("application/json");
	};
}