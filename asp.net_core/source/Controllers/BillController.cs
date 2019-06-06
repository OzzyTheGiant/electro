using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using electro.Database;
using electro.Exceptions;
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
        public async Task<ActionResult<Bill>> AddNewBill([FromBody] Bill bill) {
			dbContext.Add(bill);
			await dbContext.SaveChangesAsync();
			return CreatedAtAction(nameof(GetAllBills), new {}, bill);
        }

        [HttpPut("{id}")]
        public async Task<ActionResult<Bill>> UpdateBill(int id, [FromBody] Bill bill) {
			int rowsAffected = 0;
			try {
				bill.Id = id;
				dbContext.Update(bill);
				rowsAffected = await dbContext.SaveChangesAsync();
				return bill;
			} catch (DbUpdateConcurrencyException e) {
				if (rowsAffected == 0) return new NotFoundObjectResult(new NotFoundException("bill"));
				else throw e;
			}
        }

        [HttpDelete("{id}")]
        public async Task<IActionResult> DeleteBill(int id) {
			var bill = await dbContext.Bills.FindAsync(id);
			if (bill == null) {
				return new NotFoundObjectResult(new NotFoundException("bill"));
			}
			dbContext.Bills.Remove(bill);
			await dbContext.SaveChangesAsync();
			return NoContent();
        }
    }
}
