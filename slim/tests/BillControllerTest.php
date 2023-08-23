<?php declare(strict_types = 1);

namespace Electro\Tests;

use \Exception;
use Electro\Controllers\BillController;
use Electro\Exceptions\DatabaseException;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use PDO;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\UriFactory;
use Slim\Psr7\Response;

class BillControllerTest extends TestCase {
    protected Builder|MockObject $table;
	protected BillController $controller;
	protected ServerRequestInterface $request;
	protected ResponseInterface $response;
	protected array $data = [
        ["id" => 1, "user" => 1, "payment_amount" => 90.09, "payment_date" => "2019-06-03"],
        ["id" => 2, "user" => 1, "payment_amount" => 90.11, "payment_date" => "2019-06-04"]
    ];
    
	protected $json_data = [
        "id" => 3, 
        "user" => 1, 
        "payment_amount" => 80.08, 
        "payment_date" => "2019-06-12"
    ];

	public static function data_provider(): array {
		return [
			[
                null, 
                HttpBadRequestException::class
            ],
			[
                ["user" => 1, "payment_amount" => null, "payment_date" => "2019-20-83"], 
                DatabaseException::class
            ]
		];
	}

	protected function setUp(): void {
        $connection = $this->getMockBuilder(Connection::class)
            ->setConstructorArgs([new PDO("sqlite:memory")])
            ->getMock();

		$this->table = $this->getMockBuilder(Builder::class)
            ->setConstructorArgs([$connection])
			->onlyMethods(["get", "insert", "where", "update", "delete"])
			->getMock();
		
		$this->controller = new BillController($this->table);

        $request_factory = new ServerRequestFactory();
        $uri_factory = new UriFactory();

		$this->request = $request_factory->createServerRequest(
            "GET", $uri_factory->createUri("/api/bills")
        );

		$this->response = new Response();
	}

	public function test_bills_can_be_fetched(): void {
		$this->table->expects($this->once())->method("get")->willReturn($this->data);

		$response = $this->controller->fetchAll($this->request, $this->response);
        $response_body = (string) $response->getBody();
		$bills = json_decode($response_body, true);

		$this->assertSame(200, $response->getStatusCode());
		$this->assertSame(2, count($bills));
		$this->assertEquals(json_encode($this->data), $response_body);
	}
	
	public function test_bills_can_be_added(): void {
		$this->request = $this->request->withMethod("POST")->withParsedBody($this->json_data);
		$this->table->expects($this->once())->method("insert")->willReturn(null);

		$response = $this->controller->add($this->request, $this->response);

		$this->assertSame(201, $response->getStatusCode());
		$this->assertEquals($this->json_data, json_decode((string) $response->getBody(), true));
	}

	#[DataProvider("data_provider")]
	public function test_exceptions_thrown_when_adding_bad_data(
        array|null $data, 
        string $exception
    ): void {
		$this->request = $this->request->withParsedBody($data);
		$this->table->expects($this->any())->method("insert")->willThrowException(
            new $exception($this->request, "test")
        );

		$this->expectException($exception);
		$this->controller->add($this->request, $this->response);
	}
	
	public function test_bills_can_be_edited() {
        $bill = $this->data[0];
        $bill["payment_amount"] = 85.75;

		$this->request = $this->request->withMethod("PUT")->withParsedBody($bill);
		$this->table->expects($this->once())->method("where")->willReturnSelf();
        $this->table->expects($this->once())->method("update")->willReturn(1);
        
		$response = $this->controller->update($this->request, $this->response, [
            "id" => $bill["id"]
        ]);

		$this->assertEquals($bill, json_decode((string) $response->getBody(), true));
		$this->assertSame(200, $response->getStatusCode());
	}

	#[DataProvider("data_provider")]
	public function test_exceptions_thrown_when_submitting_bad_data_for_editing(
        array|null $data, 
        string $exception
    ): void {
		$this->request = $this->request->withMethod("PUT")->withParsedBody($data);
		$this->table->expects($this->any())->method("where")->willReturnSelf();
		$this->table->expects($this->any())->method("update")->willThrowException(
            new $exception($this->request,"test")
        );

		$this->expectException($exception);
		$this->controller->update($this->request, $this->response, ["id" => 1]);
	}

	public function test_bills_can_be_deleted() {
		$this->request = $this->request->withMethod("DELETE");
		$this->table->expects($this->any())->method("where")->willReturnSelf();
		$this->table->expects($this->any())->method("delete")->willReturn(1);

		$response = $this->controller->delete($this->request, $this->response, ["id" => 1]);
		$this->assertSame(204, $response->getStatusCode());
	}

	public function test_exception_thrown_when_deleting_bill_fails(): void {
		// set up method calls
		$this->table->expects($this->once())->method("where")->willReturnSelf();
		$this->table->expects($this->once())->method("delete")->willThrowException(
            new Exception("test")
        );

        $this->expectException(DatabaseException::class);
		$this->controller->delete($this->request, $this->response, ["id" => null]);
	}

	public function test_exception_thrown_when_bill_to_delete_is_missing(): void {
		$this->table->expects($this->once())->method("where")->willReturnSelf();
		$this->table->expects($this->once())->method("delete")->willReturn(0);
		$this->expectException(HttpNotFoundException::class);
		$this->controller->delete($this->request, $this->response, ["id" => null]);
	}
}
