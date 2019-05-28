package electro.serializers;

import electro.models.User;
import java.lang.reflect.Type;
import com.google.gson.JsonDeserializationContext;
import com.google.gson.JsonDeserializer;
import com.google.gson.JsonElement;
import com.google.gson.JsonParseException;

public class UserDeserializer implements JsonDeserializer<User> {
	@Override
	public User deserialize(JsonElement json, Type typeOfT, JsonDeserializationContext context) throws JsonParseException {
		var user = new User();
		var jsonObject = json.getAsJsonObject();
		for (var property : jsonObject.keySet()) {
			user = getValueOrNull(user, property, jsonObject.get(property));
		}
		return user;
	}

	private User getValueOrNull(User user, String key, JsonElement property) {
		/* this is needed to get lowercase key of object propertes */
		switch(key) {
			case "id": 
				user.setId(property != null ? property.getAsInt() : 0); break;
			case "username": 
				user.setUsername(property != null ? property.getAsString() : null); break;
			case "password": 
				user.setPassword(property != null ? property.getAsString() : null); break;
		} return user;
	}
}