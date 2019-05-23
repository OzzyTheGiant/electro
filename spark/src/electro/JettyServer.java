package electro;

import org.eclipse.jetty.util.thread.QueuedThreadPool;
import org.eclipse.jetty.util.thread.ThreadPool;
import org.eclipse.jetty.server.Server;
import org.eclipse.jetty.server.session.DefaultSessionCache;
import org.eclipse.jetty.server.session.FileSessionDataStore;
import org.eclipse.jetty.server.session.SessionDataStore;
import spark.ExceptionMapper;
import spark.embeddedserver.EmbeddedServerFactory;
import spark.embeddedserver.EmbeddedServers;
import spark.embeddedserver.jetty.EmbeddedJettyServer;
import spark.embeddedserver.jetty.JettyHandler;
import spark.embeddedserver.jetty.JettyServerFactory;
import spark.http.matching.MatcherFilter;
import spark.route.Routes;
import spark.staticfiles.StaticFilesConfiguration;
import static electro.App.env;

import java.io.File;

public class JettyServer {
	public static EmbeddedServerFactory embeddedServerFactory = 
	(Routes routeMatcher, StaticFilesConfiguration staticFilesConfiguration, ExceptionMapper exceptionMapper, boolean hasMultipleHandler) -> {
		// Create matcher for mapping routes and filters (middleware), then add to Jetty handler
		MatcherFilter matcherFilter = new MatcherFilter(routeMatcher, staticFilesConfiguration, exceptionMapper, false, hasMultipleHandler);
		matcherFilter.init(null);
		JettyHandler handler = new JettyHandler(matcherFilter);

		// configure session data store
		handler.setSessionCache(new DefaultSessionCache(handler));
		handler.getSessionCache().setSessionDataStore(getDataStore());

		// configure session data store
		var sessionConfig = handler.getSessionCookieConfig();
		sessionConfig.setName(env.get("SESSION_COOKIE"));
		sessionConfig.setHttpOnly(true);
		sessionConfig.setSecure(env.get("APP_ENV") != "local");
		sessionConfig.setMaxAge(Integer.parseInt(env.get("SESSION_LIFETIME")) * 60);

		/* creates embedded Jetty server. This should be the same code as JettyServer class
		   found in Spark framework. JettyServerFactory has to be implemented here because that class 
		   is not visible to external packages */
		JettyServerFactory serverFactory = new JettyServerFactory() {
			@Override
			public Server create(int maxThreads, int minThreads, int threadTimeoutMillis) {
				Server server;
				if (maxThreads > 0) {
					int max = maxThreads;
					int min = (minThreads > 0) ? minThreads : 8;
					int idleTimeout = (threadTimeoutMillis > 0) ? threadTimeoutMillis : 60000;
					server = new Server(new QueuedThreadPool(max, min, idleTimeout));
				} else {
					server = new Server();
				}
				return server;
			}
			
			@Override
			public Server create(ThreadPool threadPool) {
				return threadPool != null ? new Server(threadPool) : new Server();
			}
		};
		return new EmbeddedJettyServer(serverFactory, handler);
	};

	public static void create() {
		EmbeddedServers.add(EmbeddedServers.Identifiers.JETTY, embeddedServerFactory);
	}

	private static SessionDataStore getDataStore() {
		var sessionDataStore = new FileSessionDataStore();
		sessionDataStore.setStoreDir(new File(env.get("SPARK_SESSION_FILE_PATH")));
		return sessionDataStore;
	}
}