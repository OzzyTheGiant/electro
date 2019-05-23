package electro.controllers;

import electro.exceptions.AuthenticationException;
import electro.models.User;
import electro.services.LoginDatabaseService;
import spark.Route;
import org.mindrot.jbcrypt.BCrypt;
import static electro.App.gson;

public class LoginController {
	public static Route home = (request, response) -> {
		return "";
	};

	public static Route login = (request, response) -> {
		var jsonData = gson.fromJson(request.body(), User.class);
		var user = LoginDatabaseService.getUser(jsonData.getUsername());
		if (user != null) {
			if (BCrypt.checkpw(jsonData.getPassword(), user.getPassword())) {
				return gson.toJson(user);
			}
		} throw new AuthenticationException();
	};

	public static Route logout = (request, response) -> {
		return "";
	};
}