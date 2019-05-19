package electro.serializers;

import java.lang.reflect.Type;
import java.sql.Date;
import java.time.LocalDate;
import com.google.gson.JsonDeserializationContext;
import com.google.gson.JsonDeserializer;
import com.google.gson.JsonElement;
import com.google.gson.JsonParseException;
import electro.models.Bill;

public class BillDeserializer implements JsonDeserializer<Bill> {
	@Override
	public Bill deserialize(JsonElement json, Type typeOfT, JsonDeserializationContext context) throws JsonParseException {
		var bill = new Bill();
		var jsonObject = json.getAsJsonObject();
		for (var property : jsonObject.keySet()) {
			bill = getValueOrNull(bill, property, jsonObject.get(property));
		} return bill;
	}

	private Bill getValueOrNull(Bill bill, String key, JsonElement property) {
		switch(key) {
			case "ID": 
				bill.setId(property != null ? property.getAsInt() : 0); break;
			case "User": 
				bill.setUser(property != null ? property.getAsInt() : null); break;
			case "PaymentAmount": 
				bill.setPaymentAmount(property != null ? property.getAsBigDecimal() : null); break;
			case "PaymentDate":
				if (property == null) break;
				LocalDate localDate = LocalDate.parse(property.getAsString());
				bill.setPaymentDate(Date.valueOf(localDate));
		}
		return bill;
	}
}