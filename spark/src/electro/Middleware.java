package electro;

import java.security.SecureRandom;
import org.apache.commons.lang3.RandomStringUtils;
import electro.exceptions.AuthorizationException;
import spark.Filter;
import static electro.App.env;

public class Middleware {
	private static String[] mutatingMethods = {"POST", "PUT", "DELETE"};

	public static Filter setContentType = (request, response) -> {
		if (request.requestMethod() != "DELETE") response.type("application/json");
	};

	public static Filter checkCSRFToken = (request, response) -> {
		// Check for csrf token if method is mutating
		for (var method : mutatingMethods) {
			if (request.requestMethod().equals(method)) {
				var headerToken = request.headers("X-XSRF-TOKEN");
				if (headerToken == null || !request.session(false).attribute("csrf_token").equals(headerToken)) {
					throw new AuthorizationException();
				}
			}
		}
	};

	public static Filter createCSRFTokenCookie = (request, response) -> {
		// Generate the salt and store it in the users session and in a cookie in the response
		var token = RandomStringUtils.random(64, 0, 0, true, true, null, new SecureRandom());
		request.session(false).attribute("csrf_token", token);
		response.cookie(
			env.get("XSRF_COOKIE"), 
			token, 
			Integer.parseInt(env.get("SESSION_LIFETIME")) * 60,
			env.get("APP_ENV") != "local",
			false
		);
	};
}