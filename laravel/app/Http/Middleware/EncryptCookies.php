<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;
use Symfony\Component\HttpFoundation\Request;

class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array<int, string>
     */
    protected $except = [
        "electro-x"
    ];

    protected function decrypt(Request $request)
    {
        parent::decrypt($request);

        foreach ($request->cookies as $key => $cookie) {
            if (!$this->isDisabled($key)) continue;

            $request->cookies->set($key, $cookie);
        }

        return $request;
    }
}
