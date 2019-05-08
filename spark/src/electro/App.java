package electro;

import static spark.Spark.*;
import electro.controllers.BillController;
import electro.database.DatabaseAccessor;
import io.github.cdimascio.dotenv.Dotenv;

public class App {
	// App dependencies
	public static final Dotenv env = Dotenv.load();
	public static final DatabaseAccessor db = new DatabaseAccessor();

    public static void main(String[] args) {
		port(4567);

		get(Routes.BILLS_URL, BillController.getAllBills);

		Runtime.getRuntime().addShutdownHook(shutdownEventHandler);
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