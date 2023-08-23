<?php
namespace Electro\Tests;

use Dotenv\Dotenv;
use Electro\Controllers\LoginController;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use PDO;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Csrf\Guard as CSRFGuard;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\UriFactory;
use Slim\Psr7\Response;
use stdClass;

class LoginControllerTest extends TestCase {
	protected array $data;
	protected LoginController $controller;
	protected ServerRequestInterface $request;
	protected ResponseInterface $response;
	protected stdClass $user;
	protected Builder|MockObject $table;
    protected CSRFGuard|MockObject $guard;

	protected $json_data = ["username" => "OzzyTheGiant", "password" => 'notarealpassword'];

	public static function data_provider(): array {
		return [
			["Ozzy", "notarealpassword"],
			["OzzyTheGiant", "wrong_password"]
		];
	}

	protected function setUp(): void {
        Dotenv::createImmutable(__DIR__ . "/../")->load();

        $this->user = new stdClass;
        $this->user->id = 1;
        $this->user->username = "OzzyTheGiant";
        $this->user->password = '$argon2id$v=19$m=65536,t=3,p=4$ZeYABY+RVIf42Dx31DVhwg$Q4JkBb+fAIuFRq0ZN4b3GnP05U6Nw7XQWDTkBR2rtyk';
        
		$connection = $this->getMockBuilder(Connection::class)
            ->setConstructorArgs([new PDO("sqlite::memory:")])
            ->getMock();

		$this->table = $this->getMockBuilder(Builder::class)
            ->setConstructorArgs([$connection])
			->onlyMethods(["where", "first"])
			->getMock();
            
        $response_factory = new ResponseFactory;
        $storage = [];

        $this->guard = $this->getMockBuilder(CSRFGuard::class)
            ->setConstructorArgs([$response_factory, "csrf", &$storage])
            ->onlyMethods(["getTokenNameKey", "getTokenValueKey", "generateToken"])
            ->getMock();

		$this->controller = new LoginController($this->table, $this->guard);

		$request_factory = new ServerRequestFactory();
        $uri_factory = new UriFactory();

		$this->request = $request_factory->createServerRequest(
            "GET", $uri_factory->createUri("/api/login")
        );

		$this->response = new Response();
	}

    public function test_csrf_cookie_provided_on_home_route() {
        $this->guard->expects($this->once())->method("getTokenNameKey")->willReturn("name_key");
        $this->guard->expects($this->once())->method("getTokenValueKey")->willReturn("value_key");
		$this->guard->expects($this->once())->method("generateToken")->willReturn([
            "name_key" => "csrf",
            "value_key" => "12345678"
        ]);

		$response = $this->controller->home($this->request, $this->response);

		$this->assertSame(204, $response->getStatusCode());
        $this->assertStringContainsString(
            $_ENV["JWT_CSRF_COOKIE_NAME"], 
            $response->getHeader("Set-Cookie")[0]
        );
	}

	public function test_login_validates_credentials_and_returns_jwt_token() {
		// set up method calls
		$this->request = $this->request->withMethod("POST")->withParsedBody($this->json_data);
		$this->table->expects($this->once())->method("where")->willReturnSelf();
		$this->table->expects($this->once())->method("first")->willReturn($this->user);

		$response = $this->controller->login($this->request, $this->response);
        $cookie = $response->getHeader("Set-Cookie");
		$user = json_decode($response->getBody(), true);

		$this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString($_ENV["JWT_ACCESS_COOKIE_NAME"], $cookie[0]);
        $this->assertFalse(!empty($user["password"]));
		$this->assertSame($this->user->id, $user["id"]);
		$this->assertSame($this->user->username, $user["username"]);
	}

	#[DataProvider("data_provider")]
	public function test_login_throws_exception_if_credentials_are_invalid($username, $password) {
        $credentials = ["username" => $username, "password" => $password];
		$this->request = $this->request->withParsedBody($credentials);
		$this->table->expects($this->once())->method("where")->willReturnSelf();
		$this->table->expects($this->once())->method("first")->willReturn(null);
		$this->expectException(HttpUnauthorizedException::class);
		$this->controller->login($this->request, $this->response);
	}

	public function test_logout_destroys_jwt_cookie() {
		$response = $this->controller->logout($this->request, $this->response);

		$this->assertSame(204, $response->getStatusCode());
        $this->assertStringContainsString(
            $_ENV["JWT_ACCESS_COOKIE_NAME"], 
            $response->getHeader("Set-Cookie")[0]
        );
	}
}
