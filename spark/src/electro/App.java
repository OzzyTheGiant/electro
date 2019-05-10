package electro;

import static spark.Spark.*;
import com.google.gson.Gson;
import com.google.gson.GsonBuilder;
import electro.controllers.BillController;
import electro.database.DatabaseAccessor;
import electro.models.Bill;
import electro.serializers.BillDeserializer;
import io.github.cdimascio.dotenv.Dotenv;

public class App {
	// App dependencies
	public static final Dotenv env = Dotenv.load();
	public static final DatabaseAccessor db = new DatabaseAccessor();
	public static final Gson gson = createGson();

    public static void main(String[] args) {
		port(4567);

		get(Routes.BILLS_URL, BillController.getAllBills);
		post(Routes.BILLS_URL, BillController.addNewBill);

		after((request, response) -> {
			response.type("application/json");
		});

		Runtime.getRuntime().addShutdownHook(shutdownEventHandler);
	}

	public static <T> Gson createGson() {
		var gsonBuilder = new GsonBuilder();
		gsonBuilder.setDateFormat("yyyy-MM-dd");
		gsonBuilder.registerTypeAdapter(Bill.class, new BillDeserializer());
		return gsonBuilder.create();
	}
	
	public static Thread shutdownEventHandler = new Thread() {
		public void run() {
			try {
				db.closeConnectionPool();
			} catch (Exception e) {
				e.printStackTrace();
			}
		}
	};
}