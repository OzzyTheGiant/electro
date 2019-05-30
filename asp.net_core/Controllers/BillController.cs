using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using electro.Database;
using electro.Models;
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;

namespace electro.Controllers {
    [Route("api/bills")]
    [ApiController]
    public class BillController : ControllerBase {
		private readonly BillContext dbContext;

		public BillController(BillContext dbContext) {
			this.dbContext = dbContext;
		}

        [HttpGet]
        public async Task<IEnumerable<Bill>> GetAllBills() {
            return await dbContext.Bills.ToListAsync();
        }

        [HttpPost]
        public async Task<ActionResult<Bill>> Post([FromBody] Bill bill) {
			dbContext.Add(bill);
			await dbContext.SaveChangesAsync();
			return CreatedAtAction(nameof(GetAllBills), new {}, bill);
        }

        [HttpPut("{id}")]
        public async Task<ActionResult<Bill>> Put(int id, [FromBody] Bill bill) {
			bill.Id = id;
			dbContext.Update(bill);
			await dbContext.SaveChangesAsync();
			return bill;
        }

        [HttpDelete("{id}")]
        public async Task<IActionResult> Delete(int id) {
			var bill = await dbContext.Bills.FindAsync(id);
			if (bill == null) {
				return NotFound();
			}
			dbContext.Bills.Remove(bill);
			await dbContext.SaveChangesAsync();
			return NoContent();
        }
    }
}
