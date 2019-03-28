<?php
namespace Electro\middleware;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface as Container;
use Electro\exceptions\AuthorizationException;

class CSRFTokenMiddleware {
	protected $container;

	public function __construct(Container $container) {
		$this->container = $container;
	}

	public function __invoke(Request $request, Response $response, callable $next): Response {
		if ($this->isMutatingMethod($request->getMethod())) {
			$csrf_token = $request->getHeader("X-XSRF-TOKEN"); // returns an array with header values
			$csrf_secret = $this->container->session->getCsrfToken();
			if (empty($csrf_token) || !$csrf_secret->isValid($csrf_token[0])) { // match token against secret
				throw new AuthorizationException();
			}
		}
		$response = $next($request, $response);
		setcookie(
			$name = $_ENV["XSRF_COOKIE"], 
			$value = $this->container->session->getCsrfToken()->getValue(), 
			$expire = time() + $_ENV["SESSION_LIFETIME"] * 60, 
			$path = "/", 
			$domain = "", 
			$secure = $_ENV["APP_ENV"] === "production", 
			$httponly = true
		);
		return $response;
	}

	private function isMutatingMethod($method):bool {
		switch($method) {
			case "POST":
			case "PUT":
			case "DELETE": return true;
			default: return false;
		}
	}
}
