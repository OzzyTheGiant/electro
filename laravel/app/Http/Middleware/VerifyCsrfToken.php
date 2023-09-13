<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Symfony\Component\HttpFoundation\Cookie;

class VerifyCsrfToken extends Middleware {
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [];

    protected function newCookie($request, $config): Cookie {
        return new Cookie(
            env("JWT_CSRF_COOKIE_NAME", "electro-x"),
            $request->session()->token(),
            $this->availableAt(60 * config("jwt.ttl")),
            $config['path'],
            $config['domain'],
            $config['secure'],
            false,
            false,
            $config['same_site'] ?? null
        );
    }
}
