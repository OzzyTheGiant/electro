using System;
using Microsoft.EntityFrameworkCore;
using NUnit.Framework;
using Moq;
using electro.Models;
using electro.Database;
using electro.Controllers;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using Tests.Mocks;
using System.Threading;
using Microsoft.AspNetCore.Mvc;
using electro.Exceptions;

namespace Tests
{
	[TestFixture]
    public class BillControllerTest
    {
		private IQueryable data = new List<Bill> {
			new Bill() { Id = 1, User = 1, PaymentAmount = 90.09M, PaymentDate = DateTime.Now },
			new Bill() { Id = 2, User = 1, PaymentAmount = 90.10M, PaymentDate = DateTime.Now }
		}.AsQueryable();

		private Bill newBill = new Bill() { 
			Id = 3, 
			User = 3, 
			PaymentAmount = 100.01M, 
			PaymentDate = DateTime.Now
		};

		private Mock<DbSet<Bill>> mockDbSet;
		private Mock<BillContext> mockDbContext;
		private BillController controller;

        [SetUp]
        public void Setup() {
			// create mock data source
			mockDbSet = new Mock<DbSet<Bill>>();
			mockDbSet.As<IQueryable<Bill>>().Setup(set => set.Provider).Returns(new TestDbAsyncQueryProvider<Bill>(data.Provider));
			mockDbSet.As<IQueryable<Bill>>().Setup(set => set.Expression).Returns(data.Expression);
			mockDbSet.As<IQueryable<Bill>>().Setup(set => set.ElementType).Returns(data.ElementType);
			mockDbSet.As<IQueryable<Bill>>().Setup(set => set.GetEnumerator()).Returns((IEnumerator<Bill>) data.GetEnumerator());
			mockDbSet.As<IAsyncEnumerable<Bill>>().Setup(set => set.GetEnumerator()).Returns(new TestDbAsyncEnumerator<Bill>((IEnumerator<Bill>) data.GetEnumerator()));
			// create mock db context
			mockDbContext = new Mock<BillContext>();
			mockDbContext.Setup(context => context.Bills).Returns(mockDbSet.Object);
			// create controller with mock db context
			controller = new BillController(mockDbContext.Object);
        }

        [Test]
        public async Task GetAllBills_ReturnsListOfBills() {
            var bills = await controller.GetAllBills();
			Assert.AreEqual(2, bills.Count());
			Assert.AreEqual(1, bills.ElementAt(0).Id);
			Assert.AreEqual(2, bills.ElementAt(1).Id);
        }

		[Test]
		public async Task AddNewBill_SavesBillToDatabase() {
			var result = await controller.AddNewBill(newBill);
			Assert.IsInstanceOf<ActionResult<Bill>>(result);
			// result stores a Result property of it's child result (in this case CreatedAtActionResult)
			Assert.IsInstanceOf<CreatedAtActionResult>(result.Result);
			// Downcast ActionResult and Result.Value to the appropriate types
			Assert.AreEqual(newBill.PaymentAmount, ((Bill)((CreatedAtActionResult) result.Result).Value).PaymentAmount);
			mockDbContext.Verify(ctx => ctx.Add(It.Is<Bill>(bill => bill.PaymentAmount == newBill.PaymentAmount)), Times.Once());
			mockDbContext.Verify(ctx => ctx.SaveChangesAsync(new CancellationToken()), Times.Once());
		}

		[Test]
		public async Task UpdateBill_EditsBillInDatabase() {
			var result = await controller.UpdateBill(2, newBill);
			Assert.IsInstanceOf<ActionResult<Bill>>(result);
			// since only a model was returned in controller and not a specific action, just use result.Value
			Assert.AreEqual(newBill.PaymentAmount, ((Bill) result.Value).PaymentAmount);
			mockDbContext.Verify(ctx => ctx.Update(It.Is<Bill>(bill => bill.PaymentAmount == newBill.PaymentAmount)), Times.Once());
			mockDbContext.Verify(ctx => ctx.SaveChangesAsync(new CancellationToken()), Times.Once());
		}

		[Test]
		public async Task UpdateBill_ThrowsNotFoundErrorIfNoBillFound() {
			// set up the error to be thrown from SaveChangesAsync
			mockDbContext.Setup(ctx => ctx.SaveChangesAsync(new CancellationToken())).Throws(
				new DbUpdateException("Error updating DB", (Exception) null)
			);
			// trigger the method call and assert
			var result = await controller.UpdateBill(3, newBill);
			Assert.IsInstanceOf<ActionResult<Bill>>(result);
			Assert.IsInstanceOf<NotFoundObjectResult>(result.Result);
			mockDbContext.Verify(ctx => ctx.Update(It.Is<Bill>(bill => bill.PaymentAmount == newBill.PaymentAmount)), Times.Once());
			mockDbContext.Verify(ctx => ctx.SaveChangesAsync(new CancellationToken()), Times.Once());
		}

		[Test]
		public async Task DeleteBill_RemovesBillFromDatabase() {
			var id = 2;
			// set up the bill that will be returned from FindAsync
			mockDbContext.Setup(ctx => ctx.Bills.FindAsync(id)).ReturnsAsync(data.Cast<Bill>().ElementAt(1));
			// trigger the method call and assert
			var result = await controller.DeleteBill(id);
			Assert.IsInstanceOf<IActionResult>(result);
			Assert.IsInstanceOf<NoContentResult>(result);
			mockDbContext.Verify(ctx => ctx.Bills.Remove(It.Is<Bill>(bill => 
			bill.Id == id)), Times.Once());
			mockDbContext.Verify(ctx => ctx.SaveChangesAsync(new CancellationToken()), Times.Once());
		}

		[Test]
		public async Task DeleteBill_ThrowsNotFoundErrorIfNoBillFound() {
			var id = 3;
			// set up the bill that will be returned from FindAsync
			mockDbContext.Setup(ctx => ctx.Bills.FindAsync(id)).ReturnsAsync((Bill) null);
			// trigger the method call and assert
			var result = await controller.DeleteBill(id);
			Assert.IsInstanceOf<IActionResult>(result);
			Assert.IsInstanceOf<NotFoundObjectResult>(result);
			Assert.IsInstanceOf<NotFoundException>(((NotFoundObjectResult) result).Value);
			Assert.AreEqual(((NotFoundException) ((NotFoundObjectResult) result).Value).Code, 404);
		}
    }
}