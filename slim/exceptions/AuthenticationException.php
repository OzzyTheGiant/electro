<?php
namespace Electro\exceptions;

use \Exception;

class AuthenticationException extends Exception {
	protected $code = 401;
	protected $message = "Username or password is incorrect";

	public function __construct() {}
}