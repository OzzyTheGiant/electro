package electro.services;

import java.sql.SQLException;
import java.util.List;
import org.jooq.impl.DSL;

import electro.exceptions.DatabaseException;
import electro.exceptions.NotFoundException;
import electro.models.Bill;
import static electro.App.db;
import static jooq.generated.tables.Bills.BILLS;

public class BillDatabaseService {
	public static List<Bill> selectAllBills() throws DatabaseException {
		try (var connection = db.getConnection()) {
			return DSL.using(connection).selectFrom(BILLS).fetchInto(Bill.class);
		} catch (SQLException e) { 
			throw new DatabaseException("SQL Code: " + e.getSQLState() + ": " + e.getMessage()); 
		} catch (Exception e) {
			throw new DatabaseException(e.getMessage());
		}
	}
	
	public static Bill insertNewBill(Bill bill) throws DatabaseException {
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
		} catch (SQLException e) {
			throw new DatabaseException("SQL Code: " + e.getSQLState() + ": " + e.getMessage()); 
		} catch (Exception e) {
			throw new DatabaseException(e.getMessage());
		}
	}

	public static Bill updateBill(Bill bill) throws DatabaseException, NotFoundException {
		try (var connection = db.getConnection()) {
			var billRecord = DSL.using(connection).newRecord(BILLS, bill);
			var updated = DSL.using(connection).executeUpdate(billRecord);
			if (!(updated > 0)) {
				throw new NotFoundException("bill");
			}
			return bill;
		} catch (SQLException e) { 
			throw new DatabaseException("SQL Code: " + e.getSQLState() + ": " + e.getMessage()); 
		} catch (Exception e) {
			throw new DatabaseException(e.getMessage());
		}
	}

	public static void deleteBill(int id) throws DatabaseException, NotFoundException {
		try(var connection = db.getConnection()) {
			var deleted = DSL.using(connection).delete(BILLS).where(BILLS.ID.eq(id)).execute();
			if (!(deleted > 0)) {
				throw new NotFoundException("bill");
			}
		} catch (SQLException e) { 
			throw new DatabaseException("SQL Code: " + e.getSQLState() + ": " + e.getMessage()); 
		} catch (Exception e) {
			throw new DatabaseException(e.getMessage());
		}
	}
}