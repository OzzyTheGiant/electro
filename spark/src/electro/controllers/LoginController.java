package electro.controllers;

import electro.exceptions.AuthenticationException;
import electro.models.User;
import electro.services.LoginDatabaseService;
import spark.Route;
import org.mindrot.jbcrypt.BCrypt;
import static electro.App.gson;
import static electro.App.env;

public class LoginController {
	public static final int SESSION_LIFETIME = Integer.parseInt(env.get("SESSION_LIFETIME")) * 60;

	public static Route home = (request, response) -> {
		if (request.cookie("electro") == null) {
			request.session(true).maxInactiveInterval(SESSION_LIFETIME);
		}
		return "";
	};
	
	public static Route login = (request, response) -> {
		var jsonData = gson.fromJson(request.body(), User.class);
		var user = LoginDatabaseService.getUser(jsonData.getUsername());
		if (user != null) {
			if (BCrypt.checkpw(jsonData.getPassword(), user.getPassword())) {
				request.session(false).invalidate(); // YOU MUST SET false TO invalidate previous session
				request.session(true).maxInactiveInterval(SESSION_LIFETIME);
				request.session().attribute("current_user", user);
				return gson.toJson(user);
			}
		} throw new AuthenticationException();
	};

	public static Route logout = (request, response) -> {
		request.session(false).invalidate();
		User user = request.session(true).attribute("current_user");
		System.out.println(user == null ? null : user.getUsername());
		request.session(true).maxInactiveInterval(SESSION_LIFETIME);
		return "";
	};
}