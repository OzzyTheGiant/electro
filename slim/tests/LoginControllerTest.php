<?php
namespace Electro\tests;

use PHPUnit\Framework\TestCase;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Environment;
use Aura\Session;
use Electro\controllers\LoginController;
use Electro\models\User\UserRow;
use Electro\exceptions\AuthenticationException;

class LoginControllerTest extends TestCase {
	protected $data;
	protected $container;
	protected $controller;
	protected $request;
	protected $response;
	protected $session_segment;
	protected $user;

	protected $json_data = ["username" => "OzzyTheGiant", "password" => 'notarealpassword'];

	public function login_DataProvider(): array {
		return [
			["Ozzy", "notarealpassword"],
			["OzzyTheGiant", "wrongpassword"]
		];
	}

	protected function setUp(): void {
		// create app container
		$this->container = new Container();

		// derive a fake, mocked atlas TableLocator from stdClass so we can quickly call methods without worrying about type matching
		$this->container["atlas"] = $this->getMockBuilder(stdClass::class)
			->setMethods(["get", "select", "where", "fetchRow"])
			->getMock();

		// mock session
		$this->container["session"] = $this->getMockBuilder(Session::class)
			->setMethods(["regenerateId", "getSegment"])->getMock();

		// mock session segment
		$this->session_segment = $this->getMockBuilder(Segment::class)
			->setMethods(["set"])->getMock();

		// create controller with provided container
		$this->controller = new LoginController($this->container);

		// create user
		$this->user = new UserRow();
		$this->user->ID = 1; $this->user->Username = "OzzyTheGiant"; 
		$this->user->Password = '$2a$10$Cj66BNdUZhkMvStI5jfQoetgzSvkaQIwJuIRDPIa1zgFsFPXkbqr2';

		// set up request and response with mocked environment
		$this->request = Request::createFromEnvironment(Environment::mock());
		$this->response = new Response();
	}

	public function test_login_ValidatesCredentialsAndStartsSession() {
		// set up method calls
		$this->request = $this->request->withMethod("POST");
		$this->request = $this->request->withParsedBody($this->json_data);
		$this->container->atlas->expects($this->once())->method("get")->will($this->returnSelf());
		$this->container->atlas->expects($this->once())->method("select")->will($this->returnSelf());
		$this->container->atlas->expects($this->once())->method("where")->will($this->returnSelf());
		$this->container->atlas->expects($this->once())->method("fetchRow")->will($this->returnValue($this->user));
		$this->container->session->expects($this->once())->method("getSegment")->will($this->returnValue($this->session_segment));
		$this->session_segment->expects($this->once())->method("set");
		// perform method and assert
		$response = $this->controller->login($this->request, $this->response);
		$user = json_decode($response->getBody(), true);
		$this->assertSame($this->user->ID, $user["ID"]);
		$this->assertSame($this->user->Username, $user["Username"]);
		$this->assertSame(200, $response->getStatusCode());
	}

	/** @dataProvider login_DataProvider */
	public function test_login_ThrowsAuthenticationErrorIfCredentialsNotValid($username, $password) {
		// set up method calls
		$this->request = $this->request->withParsedBody(["username" => $username, "password" => $password]);
		$this->container->atlas->expects($this->once())->method("get")->will($this->returnSelf());
		$this->container->atlas->expects($this->once())->method("select")->will($this->returnSelf());
		$this->container->atlas->expects($this->once())->method("where")->will($this->returnSelf());
		$this->container->atlas->expects($this->once())->method("fetchRow")->will($this->returnValue(null));
		// perform method and assert
		$this->expectException(AuthenticationException::class);
		$this->controller->login($this->request, $this->response);
	}

	public function test_logout_EndsSessionAndReturnsNoContent() {
		// set up method calls
		$this->container->session->expects($this->once())->method("regenerateId");
		// perform method and assert
		$response = $this->controller->logout($this->request, $this->response);
		$this->assertSame(204, $response->getStatusCode());
	}
}