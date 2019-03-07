<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller {
	use AuthenticatesUsers;

	public function username():string {
		return 'username';
	}
	
	/**
     * Attempt to log the user into the application.
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request): bool {
        if ($this->guard()->attempt($this->credentials($request))) {
            return true;
        } return false;
	}

	/**
     * Send the response after the user was authenticated.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request): Response {
		$this->clearLoginAttempts($request);
        return $this->currentUser($request);
	}
	
	protected function sendFailedLoginResponse(\Illuminate\Http\Request $request) {
		return response()->json(["message" => "Username or password is incorrect"], 401);
	}

	protected function currentUser(Request $request) {
		return response()->json($request->user());
	}

	public function loggedOut(Request $request): Response {
		return response("", 204);
	}
}
