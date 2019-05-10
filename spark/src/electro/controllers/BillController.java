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
		var bill = gson.fromJson(request.body(), Bill.class);
		bill = BillDatabaseService.insertNewBill(bill);
		response.status(201);
		return gson.toJson(bill);
	};

	public static Route updateBill = (request, response) -> {
		var bill = gson.fromJson(request.body(), Bill.class);
		BillDatabaseService.updateBill(Integer.parseInt(request.params(":id")), bill);
		return request.body();
	};

	public static Route deleteBill = (request, response) -> {
		BillDatabaseService.deleteBill(Integer.parseInt(request.params(":id")));
		response.status(204);
		return "";
	};
}