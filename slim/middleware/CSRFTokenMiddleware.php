<?php
namespace Electro\Middleware;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Csrf\Guard as CSRFGuard;
use Slim\Exception\HttpUnauthorizedException;

class CSRFTokenMiddleware {
	protected ResponseFactoryInterface $response_factory;

	public function __construct(protected CSRFGuard $guard) {}

	public function __invoke(Request $request, RequestHandlerInterface $handler): Response {
        if ($request->getMethod() !== "GET") {
            $token = $request->getHeaderLine("X-CSRF-TOKEN");
            if (!$token) throw new HttpUnauthorizedException($request, "Missing CSRF Token");

            // The token has token name and token value separated by a dot
            $csrf_data = explode(".", $token);
            $is_valid = $this->guard->validateToken($csrf_data[0], $csrf_data[1]);
            if (!$is_valid) throw new HttpUnauthorizedException($request, "Invalid CSRF Token");
        }

        return $handler->handle($request);
    }
}
