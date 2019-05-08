package electro.controllers;

import electro.services.BillDatabaseService;
import spark.Route;

public class BillController extends Controller {
	public static Route getAllBills = (request, response) -> {
		var bills = BillDatabaseService.selectAllBills();
		response.type("application/json");
		return gson.toJson(bills);
	};
}