<?php
namespace Electro\Controllers;

use \DateTimeImmutable;
use Firebase\JWT\JWT;
use Illuminate\Database\Query\Builder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Psr7\Cookies;
use \stdClass;

class LoginController {
	public static string $table_name = "users";
    protected string $jwt_cookie_name;
    protected Cookies $cookie_manager;
    protected string | null $jwt = null;

	public function __construct(protected Builder $table) {
        $this->jwt_cookie_name = $_ENV["JWT_ACCESS_COOKIE_NAME"];
        $this->cookie_manager = new Cookies;
        $this->cookie_manager->setDefaults([
            "httponly" => true,
            "hostonly" => true,
            "secure" => $_ENV["APP_ENV"] === "production",
        ]);
    }

	public function home(Request $request, Response $response): Response {
		return $response->withStatus(204);
	}

	public function login(Request $request, Response $response): Response {
		$credentials = $request->getParsedBody();
		$user = $this->table->where("username", $credentials["username"])->first();

		if ($user) {
			if (password_verify($credentials["password"], $user->password)) {
				unset($user->password);
                $response->getBody()->write(json_encode($user));
                $response = $this->createJWTString($user)->createJWTCookie($response);
				return $response->withStatus(200)->withHeader("Content-Type", "application/json");
			}
		} 
        
        throw new HttpUnauthorizedException($request, "Username or password is not correct");
	}

	private function createJWTString(stdClass $user): self {
        $date = new DateTimeImmutable();
        $expDate = $_ENV["JWT_ACCESS_TOKEN_EXPIRES"];
        $payload = [
            "iat" => $date->getTimestamp(),
            "nbf" => $date->getTimestamp(),
            "exp" => $date->modify("+" . $expDate . " hours")->getTimestamp(),
            "id" => $user->id,
            "username" => $user->username,
        ];

		$this->jwt = JWT::encode($payload, $_ENV["JWT_SECRET_KEY"], "HS256");
        return $this;
	}

    private function createJWTCookie(Response $response): Response {
        $this->cookie_manager->set($this->jwt_cookie_name, [
            "value" => $this->jwt, 
            "expires" => (new DateTimeImmutable())->format("r")
        ]);

        return $response->withHeader("Set-Cookie", $this->cookie_manager->toHeaders());
    }

	public function logout(Request $request, Response $response): Response {
        $this->cookie_manager->set($this->jwt_cookie_name, [
            "value" => "", 
            "expires" => "Thu, 01 Jan 1970 00:00:00 GM"
        ]);

		$response = $response->withHeader("Set-Cookie", $this->cookie_manager->toHeaders());
		return $response->withStatus(204);
	}
}