<?php
namespace Electro\tests;

use PHPUnit\Framework\TestCase;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Electro\controllers\BillController;
use Electro\models\Bill\BillRow;
use Slim\Http\Environment;
use Electro\exceptions\EmptyRequestBodyException;
use Electro\exceptions\ValidationException;
use Electro\exceptions\DatabaseException;
use Electro\exceptions\NotFoundException;

class BillControllerTest extends TestCase {
	protected $data;
	protected $container;
	protected $controller;
	protected $request;
	protected $response;

	protected $json_data = ["ID" => 3, "User" => 1, "PaymentAmount" => 80.08, "PaymentDate" => "2019-06-12"];

	public function edit_DataProvider(): array {
		return [
			[null, EmptyRequestBodyException::class],
			[["User" => null, "PaymentAmount" => null, "PaymentDate" => null], ValidationException::class],
			[["User" => 1, "PaymentAmount" => 80.08, "PaymentDate" => "2019-05-20"], DatabaseException::class]
		];
	}

	protected function setUp(): void {
		// create app container
		$this->container = new Container();

		// derive a fake, mocked atlas TableLocator from stdClass so we can quickly call methods without worrying about type matching
		$this->container["atlas"] = $this->getMockBuilder(stdClass::class)
			->setMethods(["get", "select", "orderBy", "fetchRows", "newRow", "insertRow", "fetchRow", "updateRow", "deleteRow"])
			->getMock();

		// setup data source
		$bill1 = new BillRow();
		$bill2 = new BillRow();
		$bill1->ID = 1; $bill1->User = 1; $bill1->PaymentAmount = 90.09; $bill1->PaymentDate = "2019-06-03";
		$bill2->ID = 2; $bill2->User = 1; $bill2->PaymentAmount = 90.11; $bill2->PaymentDate = "2019-06-04";
		$this->data = [$bill1, $bill2];

		// create BillRow generator function for all tests that need it
		$this->create_new_bill = function (array $data): BillRow {
			if (!$data) throw new \TypeError();
			$value = null; $key = null;
			foreach($data as $key => $value) {
				if (!$value) throw new ValidationException($key, ValidationException::TYPES["required"]);
			}
			$bill = new BillRow();
			$bill->ID = 3; 
			$bill->User = $data["User"]; 
			$bill->PaymentDate = $data["PaymentDate"];
			$bill->PaymentAmount = $data["PaymentAmount"]; 
			return $bill;
		};
		
		// create controller with provided container
		$this->controller = new BillController($this->container);

		// set up request and response with mocked environment
		$this->request = Request::createFromEnvironment(Environment::mock());
		$this->response = new Response();
	}

	public function test_getAllBills_ReturnsListofBills(): void {
		// set up method that will return data;
		$this->container->atlas->expects($this->once())->method("get")->will($this->returnSelf());
		$this->container->atlas->expects($this->once())->method("select")->will($this->returnSelf());
		$this->container->atlas->expects($this->once())->method("orderBy")->will($this->returnSelf());
		$this->container->atlas->expects($this->once())->method("fetchRows")->will($this->returnValue($this->data));
		// perform method and assert
		$response = $this->controller->getAllBills($this->request, $this->response, "PaymentDate DESC");
		$bills = json_decode($response->getBody());
		$this->assertSame(2, count($bills));
		$this->assertEquals(json_encode($this->data), $response->getBody());
		$this->assertSame(200, $response->getStatusCode());
	}
	
	public function test_add_CreatesNewBill(): void {
		// set up method calls and provide request body
		$this->request = $this->request->withMethod("POST");
		$this->request = $this->request->withParsedBody($this->json_data);
		$this->container->atlas->expects($this->once())->method("get")->will($this->returnSelf());
		$this->container->atlas->expects($this->once())->method("newRow")->will($this->returnCallback($this->create_new_bill));
		// perform method and assert
		$response = $this->controller->add($this->request, $this->response);
		$this->assertEquals($this->json_data, json_decode($response->getBody(), true));
		$this->assertSame(201, $response->getStatusCode());
	}

	/** @dataProvider edit_DataProvider */
	public function test_add_ThrowsExceptionsIfDataNotProvidedOrDbErrorHappens($data, $exception): void {
		// set up method calls and the exception to be thrown
		$this->request = $this->request->withParsedBody($data);
		$this->container->atlas->expects($this->once())->method("get")->will($this->returnSelf());
		$this->container->atlas->expects($this->once())->method("newRow")->will($this->returnCallback($this->create_new_bill));
		$this->container->atlas->expects($this->any())->method("insertRow")->will($this->throwException(new \Exception("db error")));
		// expect exception and perform method
		$this->expectException($exception);
		$this->controller->add($this->request, $this->response);
	}
	
	public function test_update_EditsBill() {
		// set up method calls and provide request body
		$this->request = $this->request->withMethod("PUT");
		$this->request = $this->request->withParsedBody($this->json_data);
		$this->container->atlas->expects($this->once())->method("get")->will($this->returnSelf());
		$this->container->atlas->expects($this->once())->method("fetchRow")->will($this->returnCallback(function() { 
			return ($this->create_new_bill)($this->json_data); // must be wrapped because it's not a method but a callback function
		}));
		// perform method and assert
		$response = $this->controller->update($this->request, $this->response, ["id" => 3]);
		$this->assertEquals($this->json_data, json_decode($response->getBody(), true));
		$this->assertSame(200, $response->getStatusCode());
	}

	/** @dataProvider edit_DataProvider */
	public function test_update_ThrowsExceptionsIfDataNotProvidedOrDbErrorHappens($data, $exception) {
		// set up method calls and exception to be thrown
		$this->request = $this->request->withParsedBody($data);
		$this->container->atlas->expects($this->any())->method("get")->will($this->returnSelf());
		$this->container->atlas->expects($this->any())->method("fetchRow")->will($this->returnCallback(function() use ($data){ 
			return ($this->create_new_bill)($data); // must be wrapped because it's not a method but a callback function
		}));
		$this->container->atlas->expects($this->any())->method("updateRow")->will($this->throwException(new \Exception("db error")));
		// perform method and expect exception
		$this->expectException($exception);
		$this->controller->update($this->request, $this->response, ["id" => 3]);
	}

	public function test_update_ThrowsNotFoundExceptionIfBillIdNotSpecifiedOrBillNonExistent() {
		// set up method calls and exception to be thrown
		$this->request = $this->request->withParsedBody($this->json_data);
		$this->container->atlas->expects($this->any())->method("get")->will($this->returnSelf());
		$this->container->atlas->expects($this->any())->method("fetchRow")->will($this->throwException(new \TypeError()));
		// perform method and expect exception
		$this->expectException(NotFoundException::class);
		$this->controller->update($this->request, $this->response, ["id" => null]);
	}

	public function test_delete_RemovesBillFromDatabase() {
		$this->request = $this->request->withMethod("DELETE");
		$this->container->atlas->expects($this->any())->method("get")->will($this->returnSelf());
		$this->container->atlas->expects($this->any())->method("fetchRow")->will($this->returnCallback(function() { 
			return ($this->create_new_bill)($this->json_data); // must be wrapped because it's not a method but a callback function
		}));
		// perform method and expect exception
		$response = $this->controller->delete($this->request, $this->response, ["id" => 3]);
		$this->assertSame(204, $response->getStatusCode());
	}

	public function test_delete_ThrowsNotFoundExceptionIfIdNotSpecifiedOrBillNonExistent() {
		// set up method calls
		$this->container->atlas->expects($this->any())->method("get")->will($this->returnSelf());
		$this->container->atlas->expects($this->any())->method("fetchRow")->will($this->throwException(new \TypeError()));
		// perform method and expect exception
		$this->expectException(NotFoundException::class);
		$this->controller->delete($this->request, $this->response, ["id" => null]);
	}

	public function test_delete_ThrowsDatabaseExceptionIfSystemErrorHappens() {
		// set up method calls
		$this->container->atlas->expects($this->any())->method("get")->will($this->returnSelf());
		$this->container->atlas->expects($this->any())->method("fetchRow")->will($this->returnCallback(function() { 
			return ($this->create_new_bill)($this->json_data); // must be wrapped because it's not a method but a callback function
		}));
		$this->container->atlas->expects($this->any())->method("get")->will($this->throwException(new \Exception("db error")));
		// perform method and expect exception
		$this->expectException(DatabaseException::class);
		$this->controller->delete($this->request, $this->response, ["id" => null]);
	}
}