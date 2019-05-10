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
		LocalDate localDate = LocalDate.parse(jsonObject.get("PaymentDate").getAsString());
		bill.setPaymentDate(Date.valueOf(localDate));
		bill.setPaymentAmount(jsonObject.get("PaymentAmount").getAsBigDecimal());
		bill.setUser(jsonObject.get("User").getAsInt());
		var id = jsonObject.get("ID");
		if (id != null) bill.setId(id.getAsInt());
		return bill;
	}
}