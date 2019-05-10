package electro.controllers;

import electro.models.Bill;
import electro.services.BillDatabaseService;
import spark.Route;
import static electro.App.gson;

public class BillController {
	public static Route getAllBills = (request, response) -> {
		var bills = BillDatabaseService.selectAllBills();
		return gson.toJson(bills);
	};

	public static Route addNewBill = (request, response) -> {
		var json = request.body();
		var bill = gson.fromJson(json, Bill.class);
		bill = BillDatabaseService.insertNewBill(bill);
		response.status(201);
		return gson.toJson(bill);
	};
}