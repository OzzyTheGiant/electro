package electro.services;

import electro.exceptions.DatabaseException;
import electro.models.User;
import org.jooq.impl.DSL;
import java.sql.SQLException;
import static electro.App.db;
import static jooq.generated.tables.Users.USERS;

public class LoginDatabaseService {
	public static User getUser(String username) throws DatabaseException {
		try (var connection = db.getConnection()) {
			return DSL.using(connection)
				.selectFrom(USERS)
				.where(USERS.USERNAME.eq(username))
				.fetchAny().into(User.class);
		} catch (SQLException e) { 
			throw new DatabaseException("SQL Code: " + e.getSQLState() + ": " + e.getMessage()); 
		} catch (Exception e) {
			throw new DatabaseException(e.getMessage());
		}
	} 
}