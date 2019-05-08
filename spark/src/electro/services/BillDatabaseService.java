package electro.services;

import java.sql.SQLException;
import java.util.List;
import org.jooq.impl.DSL;
import electro.models.Bill;
import static electro.App.db;

public class BillDatabaseService {
	public static final String TABLE = "Bills";

	public static List<Bill> selectAllBills() throws SQLException {
		try(var connection = db.getConnection()) {
			return DSL.using(connection).selectFrom(TABLE).fetchInto(Bill.class);
		} catch (Exception e) { throw e; }
	}
}