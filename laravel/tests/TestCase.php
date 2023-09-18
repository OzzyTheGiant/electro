<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    public function setUp(): void {
        parent::setUp();

        $this->seed(TestSeeder::class);

        $session_cookie_name = env("SESSION_COOKIE", "electro-s");
        $csrf_cookie_name = env("JWT_CSRF_COOKIE_NAME", "electro-x");
        $jwt_cookie_name = env("JWT_ACCESS_COOKIE_NAME", "electro");

        $response = $this->get("/api/home");
        $session_cookie = $response->getCookie($session_cookie_name);
        $csrf_token = $response->getCookie($csrf_cookie_name, false);

        $this->session_string = $session_cookie->getValue();
        $this->csrf_token = $csrf_token->getValue();

        $response = $this->post(
            "/api/login",
            ["username" => "OzzyTheGiant", "password" => "notarealpassword"],
            [
                "Accept" => "application/json",
                "Cookie" => "$session_cookie_name=$this->session_string",
                "X-CSRF-TOKEN" => $this->csrf_token
            ]
        );

        $this->jwt_string = $response->getCookie($jwt_cookie_name);
    }
}
