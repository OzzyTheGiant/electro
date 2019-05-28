package electro;

import java.net.URI;
import org.apache.logging.log4j.Level;
import org.apache.logging.log4j.core.LoggerContext;
import org.apache.logging.log4j.core.appender.ConsoleAppender;
import org.apache.logging.log4j.core.config.Configuration;
import org.apache.logging.log4j.core.config.ConfigurationFactory;
import org.apache.logging.log4j.core.config.ConfigurationSource;
import org.apache.logging.log4j.core.config.Order;
import org.apache.logging.log4j.core.config.builder.api.ConfigurationBuilder;
import org.apache.logging.log4j.core.config.builder.impl.BuiltConfiguration;
import org.apache.logging.log4j.core.config.plugins.Plugin;
import static electro.App.env;

@Plugin(name = "LoggerFactory", category = ConfigurationFactory.CATEGORY)
@Order(1)
public class LoggerFactory extends ConfigurationFactory {

    static Configuration createConfiguration(final String name, ConfigurationBuilder<BuiltConfiguration> builder) {
        builder.setConfigurationName(name);
		builder.setStatusLevel(Level.INFO);

		// Create format with pattern "Date [Thread} Level LoggerName - Message"
		var format = builder.newLayout("PatternLayout")
			.addAttribute("pattern", "[%d] [%t] %-5level %logger{36} - %msg%n");
		
		// create and add appenders to configuration (log handlers). Attributes are equivalent to XML attributes in XML configs
		var consoleAppender = builder.newAppender("STDOUT", "Console")
			.addAttribute("target", ConsoleAppender.Target.SYSTEM_OUT)
			.add(format);
		var fileAppender = builder.newAppender("LOGFILE", "File")
			.addAttribute("fileName", env.get("LOG_FILE_PATH"))
			.addAttribute("immediateFlush", true) // to output logs immediately instead of when process finishes
			.addAttribute("append", true)
			.add(format);

		builder.add(consoleAppender);
		builder.add(fileAppender);

		// create logger object with appender configurations
		builder.add(builder.newLogger("electro", Level.INFO)
			.addAttribute("additivity", false)
			.add(builder.newAppenderRef("LOGFILE"))
			.add(builder.newAppenderRef("STDOUT"))
		);

		// create root logger to capture log events from dependencies
		builder.add(builder.newRootLogger(Level.INFO)
			.add(builder.newAppenderRef("STDOUT"))
		);
        return builder.build();
    }

    @Override
    public Configuration getConfiguration(final LoggerContext loggerContext, final ConfigurationSource source) {
        return getConfiguration(loggerContext, source.toString(), null);
    }

    @Override
    public Configuration getConfiguration(final LoggerContext loggerContext, final String name, final URI configLocation) {
		ConfigurationBuilder<BuiltConfiguration> builder = newConfigurationBuilder();
        return createConfiguration(name, builder);
    }

    @Override
    protected String[] getSupportedTypes() {
        return new String[] {"*"};
    }
}