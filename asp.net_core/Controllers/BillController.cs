using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using electro.Database;
using electro.Models;
using Microsoft.AspNetCore.Mvc;

namespace electro.Controllers {
    [Route("api/bills")]
    [ApiController]
    public class BillController : ControllerBase {
		private readonly BillContext dbContext;

		public BillController(BillContext dbContext) {
			this.dbContext = dbContext;
		}

        [HttpGet]
        public IEnumerable<Bill> GetAllBills() {
            return dbContext.Bills;
        }

        [HttpPost]
        public void Post([FromBody] Bill bill) {
        }

        [HttpPut("{id}")]
        public void Put(int id, [FromBody] string value) {
        }

        [HttpDelete("{id}")]
        public void Delete(int id) {
        }
    }
}
