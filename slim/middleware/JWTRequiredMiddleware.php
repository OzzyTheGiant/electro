<?php
namespace Electro\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Database\Query\Builder;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpForbiddenException;
use Slim\Psr7\Cookies;

class JWTRequiredMiddleware {
    public function __construct(private Builder $table) {}

	public function __invoke(Request $request, RequestHandler $handler): Response {
        $url = $request->getUri()->getPath();

        if ($url === "/api" || $url === "/api/login") return $handler->handle($request);

        $cookie_manager = new Cookies($request->getCookieParams());
        $token = $cookie_manager->get($_ENV["JWT_ACCESS_COOKIE_NAME"]);
        $error = new HttpForbiddenException(
            $request, 
            "You are not authorized to perform this action"
        );

        if (!$token) throw $error;
        
        $jwt_data = (array) JWT::decode($token, new Key($_ENV["JWT_SECRET_KEY"], "HS256"));

        if (!$jwt_data || !isset($jwt_data["id"]) || !isset($jwt_data["username"])) {
            throw $error;
        }

        $user = $this->table
            ->where("id", $jwt_data["id"])
            ->where("username", $jwt_data["username"])
            ->first();

        if (!$user) throw $error;

		return $handler->handle($request);
	}
}
