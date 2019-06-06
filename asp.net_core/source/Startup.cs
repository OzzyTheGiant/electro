using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using System.Text;
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Builder;
using Microsoft.AspNetCore.Hosting;
using Microsoft.AspNetCore.Mvc;
using Microsoft.Extensions.Configuration;
using Microsoft.Extensions.DependencyInjection;
using Microsoft.Extensions.Logging;
using Microsoft.Extensions.Options;
using Microsoft.EntityFrameworkCore;
using electro.Database;
using electro.Controllers;
using dotenv.net.DependencyInjection.Extensions;
using Microsoft.AspNetCore.Antiforgery;
using electro.Middleware;

namespace electro {
    public class Startup {
		public string connectionString {
			get {
				return 
					"server=" + Environment.GetEnvironmentVariable("DB_HOST") +
					";port=" + Environment.GetEnvironmentVariable("DB_PORT") +
					";database=" + Environment.GetEnvironmentVariable("DB_DATABASE") +
					";uid=" + Environment.GetEnvironmentVariable("DB_USERNAME") +
					";password=" + Environment.GetEnvironmentVariable("DB_PASSWORD");
			}
		}

		public int sessionLifetime { get; private set; }

        public Startup(IConfiguration configuration)
        {
            Configuration = configuration;
        }

        public IConfiguration Configuration { get; }

        // This method gets called by the runtime. Use this method to add services to the container.
        public void ConfigureServices(IServiceCollection services)
        {
			// load .env file
			services.AddEnv(builder => {
				builder
					.AddEnvFile(Environment.GetEnvironmentVariable("ENV_FILE"))
					.AddThrowOnError(false)
					.AddEncoding(Encoding.UTF8);
			});
			
			// add Entity db contexts
			services.AddDbContext<UserContext>(options => options.UseMySql(connectionString));
			services.AddDbContext<BillContext>(options => options.UseMySql(connectionString));

			// implement sessions
			sessionLifetime = Int32.Parse(Environment.GetEnvironmentVariable("SESSION_LIFETIME"));
			var isSecure = Environment.GetEnvironmentVariable("APP_ENV") == "local" ? CookieSecurePolicy.None : CookieSecurePolicy.Always;
			services.AddDistributedMemoryCache();
			services.AddSession(options => {
				options.IdleTimeout = TimeSpan.FromMinutes(sessionLifetime);
				options.Cookie.Name = Environment.GetEnvironmentVariable("SESSION_COOKIE");
				options.Cookie.HttpOnly = true;
				options.Cookie.MaxAge = TimeSpan.FromMinutes(sessionLifetime);
				options.Cookie.SecurePolicy = isSecure;
				options.Cookie.IsEssential = true;
			});

			// add csrf protection
			services.AddAntiforgery(options => {
				// Set Cookie properties using CookieBuilder properties
				options.HeaderName = "X-XSRF-TOKEN";
				options.SuppressXFrameOptionsHeader = true;
			});

			// load ASP.NET MVC framework
            services.AddMvc()
				.SetCompatibilityVersion(CompatibilityVersion.Version_2_2)
				.AddJsonOptions(options => options.SerializerSettings.DateFormatString = "yyyy-MM-dd");

			// create custom json response for validation error messages
			services.Configure<ApiBehaviorOptions>(options => options.InvalidModelStateResponseFactory = ErrorController.validationResponseFactory);
        }

        // This method gets called by the runtime. Use this method to configure the HTTP request pipeline (Middlewares)
        public void Configure(IApplicationBuilder app, IHostingEnvironment env, IAntiforgery antiforgery, ILoggerFactory loggerFactory) {
			loggerFactory.AddFile(Environment.GetEnvironmentVariable("LOG_FILE_PATH").Trim(new Char[] {'"'}));
			// The default HSTS value is 30 days. You may want to change this for production scenarios
            if (!env.IsDevelopment()) app.UseHsts();  
			app.UseGlobalExceptionHandler(); // Custom exception handler for uncaught exceptions
			app.UseStatusCodePagesWithReExecute("/errors/{0}"); // for when urls are not found or a generic error response is needed
			app.UseCSRFManager();
			app.UseSession();
            app.UseMvc();
        }
    }
}
