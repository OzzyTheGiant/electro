package electro.database;

import java.sql.Connection;
import java.sql.SQLException;
import com.zaxxer.hikari.HikariDataSource;
import static electro.App.env;

public class DatabaseAccessor {
	private final String CONNECTION_STRING = "jdbc:%1$s://%2$s:%3$s/%4$s?useUnicode=true&useSSL=false&useJDBCCompliantTimezoneShift=true&useLegacyDatetimeCode=false&serverTimezone=%5$s";
	private final HikariDataSource datasource = new HikariDataSource();

	public DatabaseAccessor() {
		var url = String.format(
			CONNECTION_STRING, 
			env.get("DB_CONNECTION"), 
			env.get("DB_HOST"),
			env.get("DB_PORT"), 
			env.get("DB_DATABASE"),
			env.get("SERVER_TIMEZONE")
		);

		this.datasource.setJdbcUrl(url);
		this.datasource.setUsername(env.get("DB_USERNAME"));
		this.datasource.setPassword(env.get("DB_PASSWORD"));
		this.datasource.setMaximumPoolSize(Integer.parseInt(env.get("CP_MAX_POOL_SIZE", "10")));
		if (env.get("DB_CONNECTION").equals("mysql")) {
			this.datasource.addDataSourceProperty("useServerPrepStmts", env.get("MYSQL_PREP_STMT"));
			this.datasource.addDataSourceProperty("cachePrepStmts", env.get("MYSQL_CACHE_PREP_STMTS"));
			this.datasource.addDataSourceProperty("prepStmtCacheSize", env.get("MYSQL_PREP_STMT_CACHE_SIZE"));
			this.datasource.addDataSourceProperty("prepStmtCacheSqlLimit", env.get("MYSQL_PREP_STMT_CACHE_SQL_LIMIT"));
		}
	}

	public Connection getConnection() throws SQLException {
		return this.datasource.getConnection();
	}

	public void closeConnectionPool() throws SQLException {
		this.datasource.close();
	}
}