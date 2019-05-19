package electro;

import static spark.Spark.*;
import com.google.gson.Gson;
import com.google.gson.GsonBuilder;
import electro.controllers.BillController;
import electro.database.DatabaseAccessor;
import electro.models.Bill;
import electro.serializers.BillDeserializer;
import electro.services.ValidationService;
import electro.exceptions.HttpException;
import io.github.cdimascio.dotenv.Dotenv;
import spark.ExceptionHandler;

public class App {
	// App dependencies
	public static final Dotenv env = Dotenv.load();
	public static final DatabaseAccessor db = new DatabaseAccessor();
	public static final Gson gson = createGson();
	public static final ValidationService validator = new ValidationService();

    public static void main(String[] args) {
		port(4567);

		before((request, response) -> {
			if (request.requestMethod() != "DELETE") response.type("application/json");
		});

		get(Routes.BILLS_URL, BillController.getAllBills);
		post(Routes.BILLS_URL, BillController.addNewBill);
		put(Routes.BILLS_URL + "/:id", BillController.updateBill);
		delete(Routes.BILLS_URL + "/:id", BillController.deleteBill);

		// notFound((request, response) -> {

		// });
		exception(Exception.class, exceptionHandler);

		Runtime.getRuntime().addShutdownHook(shutdownEventHandler);
	}

	public static ExceptionHandler<Exception> exceptionHandler = (exception, request, response) -> {
		var statusCode = 500;
		var errorMessage = exception.getMessage();
		exception.printStackTrace();
		if (exception instanceof HttpException) {
			HttpException httpException = (HttpException) exception;
			statusCode = httpException.getStatusCode();
			errorMessage = httpException.getMessage();
		}
		response.status(statusCode);
		response.body(String.format("{\"message\": \"%1$s\"}", errorMessage));
	};

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