using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using System.Text;
using Microsoft.AspNetCore.Builder;
using Microsoft.AspNetCore.Hosting;
using Microsoft.AspNetCore.HttpsPolicy;
using Microsoft.AspNetCore.Mvc;
using Microsoft.Extensions.Configuration;
using Microsoft.Extensions.DependencyInjection;
using Microsoft.Extensions.Logging;
using Microsoft.Extensions.Options;
using Microsoft.EntityFrameworkCore;
using electro.Database;
using electro.Controllers;
using dotenv.net.DependencyInjection.Extensions;

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
        public Startup(IConfiguration configuration)
        {
            Configuration = configuration;
        }

        public IConfiguration Configuration { get; }

        // This method gets called by the runtime. Use this method to add services to the container.
        public void ConfigureServices(IServiceCollection services)
        {
			services.AddEnv(builder => {
				builder
					.AddEnvFile("../.env")
					.AddThrowOnError(false)
					.AddEncoding(Encoding.UTF8);
			});
			services.AddDbContext<BillContext>(options => options.UseMySql(connectionString));
            services.AddMvc()
				.SetCompatibilityVersion(CompatibilityVersion.Version_2_2)
				.AddJsonOptions(options => options.SerializerSettings.DateFormatString = "yyyy-MM-dd");
			// create custom json response for validation error messages
			services.Configure<ApiBehaviorOptions>(options => options.InvalidModelStateResponseFactory = ErrorController.validationResponseFactory);
        }

        // This method gets called by the runtime. Use this method to configure the HTTP request pipeline.
        public void Configure(IApplicationBuilder app, IHostingEnvironment env)
        {
            if (env.IsDevelopment()) app.UseDeveloperExceptionPage();
            // The default HSTS value is 30 days. You may want to change this for production scenarios, see https://aka.ms/aspnetcore-hsts.
            else app.UseHsts();
			app.UseStatusCodePagesWithReExecute("/errors/{0}");
            app.UseMvc();
        }
    }
}
