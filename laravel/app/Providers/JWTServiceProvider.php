<?php

namespace App\Providers;

use PHPOpenSourceSaver\JWTAuth\Http\Parser\AuthHeaders;
use PHPOpenSourceSaver\JWTAuth\Http\Parser\Cookies;
use PHPOpenSourceSaver\JWTAuth\Http\Parser\InputSource;
use PHPOpenSourceSaver\JWTAuth\Http\Parser\QueryString;
use PHPOpenSourceSaver\JWTAuth\Http\Parser\RouteParams;
use PHPOpenSourceSaver\JWTAuth\Providers\LaravelServiceProvider;

class JWTServiceProvider extends LaravelServiceProvider {
    public function boot(): void {
        parent::boot();

        $this->app['tymon.jwt.parser']->setChain([
            new AuthHeaders,
            new QueryString,
            new InputSource,
            new RouteParams,
            (new Cookies(config('jwt.decrypt_cookies')))
                ->setKey(env("JWT_ACCESS_COOKIE_NAME"))
        ]);

    }
}
