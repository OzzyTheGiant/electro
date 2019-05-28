package electro.controllers;

import electro.models.Bill;
import electro.services.BillDatabaseService;
import spark.Route;
import java.time.format.DateTimeParseException;
import electro.exceptions.EmptyRequestBodyException;
import electro.exceptions.ValidationException;
import static electro.App.gson;
import static electro.App.validator;

public class BillController {
	public static Route getAllBills = (request, response) -> {
		var bills = BillDatabaseService.selectAllBills();
		return gson.toJson(bills);
	};

	public static Route addNewBill = (request, response) -> {
		var bill = processJSON(request.body());
		bill = BillDatabaseService.insertNewBill(bill);
		response.status(201);
		return gson.toJson(bill);
	};

	public static Route updateBill = (request, response) -> {
		var bill = processJSON(request.body());
		if (bill.getId() == 0) {
			bill.setId(Integer.parseInt(request.params(":id")));
		} BillDatabaseService.updateBill(bill);
		return request.body();
	};

	public static Route deleteBill = (request, response) -> {
		BillDatabaseService.deleteBill(Integer.parseInt(request.params(":id")));
		response.status(204);
		return "";
	};

	private static Bill processJSON(String json) 
		throws ValidationException, EmptyRequestBodyException {
		try {
			if (json.equals("")) {
				throw new EmptyRequestBodyException();
			}
			var bill = gson.fromJson(json, Bill.class);
			var message = validator.validate(bill);
			if (message != "") {
				throw new ValidationException(message);
			}
			return bill;
		} catch (DateTimeParseException e) {
			throw new ValidationException("Payment Date is an invalid date");
		}
	}
}