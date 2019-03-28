<?php
namespace Electro\middleware;

use Psr\Container\ContainerInterface as Container;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class SessionMiddleware {
	protected $container;

	public function __construct(Container $container){
		$this->container = $container;
	}

	public function __invoke(Request $request, Response $response, callable $next): Response {
		$response = $next($request, $response);
		/* check that session cookie was sent so that we can send back, otherwise no session cookie will be
		returned because Aura Session won't create cookie for a session already set */
		if (isset($_COOKIE[$_ENV["SESSION_COOKIE"]])) setcookie(
			$name = $_ENV["SESSION_COOKIE"],
			$value = $this->container->session->getId(),
			$expire = time() + $_ENV["SESSION_LIFETIME"] * 60,
			$path = "/",
			$secure = $_ENV["APP_ENV"] !== 'local',
			$httponly = true
		);
		return $response;
	}
}