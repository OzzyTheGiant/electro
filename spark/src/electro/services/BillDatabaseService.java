package electro.services;

import java.sql.SQLException;
import java.util.List;
import org.jooq.impl.DSL;
import electro.models.Bill;
import static electro.App.db;
import static jooq.generated.tables.Bills.BILLS;

public class BillDatabaseService {
	public static List<Bill> selectAllBills() throws SQLException {
		try (var connection = db.getConnection()) {
			return DSL.using(connection).selectFrom(BILLS).fetchInto(Bill.class);
		} catch (Exception e) { throw e; }
	}

	public static Bill insertNewBill(Bill bill) throws SQLException {
		try (var connection = db.getConnection()) {
			var id = DSL.using(connection).insertInto(BILLS)
				.set(BILLS.USER, bill.getUser())
				.set(BILLS.PAYMENTAMOUNT, bill.getPaymentAmount())
				.set(BILLS.PAYMENTDATE, bill.getPaymentDate())
				.returning(BILLS.ID) // specify to return the ID number
				.fetchOne() // execute query and get resulting record
				.getValue(BILLS.ID);
			bill.setId(id);
			return bill;
		} catch (Exception e) { throw e; }
	}

	public static Bill updateBill(int id, Bill bill) throws SQLException {
		try (var connection = db.getConnection()) {
			var billRecord = DSL.using(connection).newRecord(BILLS, bill);
			DSL.using(connection).executeUpdate(billRecord);
			return bill;
		} catch (Exception e) { throw e; }
	}

	public static void deleteBill(int id) throws SQLException {
		try(var connection = db.getConnection()) {
			var deleted = DSL.using(connection).delete(BILLS).where(BILLS.ID.eq(id)).execute();
			if (!(deleted > 0)) {
				// TODO: implement not found exception
				throw new SQLException("The specified bill could not be found");
			}
		} catch (Exception e) { throw e; }
	}
}