<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\User as UserResource;
use DateTime;
use DateTimeImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller {
    public function __construct() {
        $this->middleware("auth:api", ["except" => ["home", "login"]]);
        $this->middleware("web");
    }

    /** This method is purely to send back a CSRF Token */
    public function home(Request $request): Response {
        return response("", 204);
    }

    public function login(Request $request): Response {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required']
        ]);

        $token = Auth::attempt($credentials);

        if ($token) {
            $minutes = env("JWT_ACCESS_TOKEN_EXPIRES");
            $response = (new UserResource(Auth::user()))->response();
            $response = $response->withCookie(cookie(
                env("JWT_ACCESS_COOKIE_NAME"),
                $token,
                $minutes,
                httpOnly: true
            ));

            return $response;
        }

        throw ValidationException::withMessages([
            "username" => "Username or password is incorrect"
        ]);
    }

    function getCurrentUser(Request $request) {
        return $request->user();
    }

	public function logout(Request $request): Response {
        Auth::logout();
        $response = response("", 204);

        return $response->cookie(new Cookie(
            env("JWT_ACCESS_COOKIE_NAME", "electro"),
            "",
            0
        ));
	}
}
