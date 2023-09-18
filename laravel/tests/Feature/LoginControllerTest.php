<?php

namespace Tests;

use DateTime;
use Tests\TestCase;

class LoginControllerTest extends TestCase {
    /**
     * A basic test example.
     */
    public function test_login_is_successful(): void {
        $this->assertNotEmpty($this->jwt_string);
    }

    public function test_login_fails_if_credentials_missing(): void {
        $session_cookie_name = env("SESSION_COOKIE", "electro-s");
        $response = $this->post("/api/login", [], [
            "Accept" => "application/json",
            "Cookie" => "$session_cookie_name=$this->session_string",
            "X-CSRF-TOKEN" => $this->csrf_token
        ]);

        $response->assertStatus(422);
    }

    public function test_login_fails_if_credentials_incorrect(): void {
        $session_cookie_name = env("SESSION_COOKIE", "electro-s");
        $response = $this->post("/api/login", ["username" => "test", "password" => "test"], [
            "Accept" => "application/json",
            "Cookie" => "$session_cookie_name=$this->session_string",
            "X-CSRF-TOKEN" => $this->csrf_token
        ]);

        $response->assertStatus(422);
    }

    public function test_logout_is_successful(): void {
        $jwt_cookie_name = env("JWT_ACCESS_COOKIE_NAME", "electro");
        $response = $this->post("/api/logout");
        $response->assertStatus(204);
        $exp_time = $response->getCookie($jwt_cookie_name)->getExpiresTime();
        $this->assertLessThan((new DateTime)->getTimestamp() , $exp_time);
    }
}
