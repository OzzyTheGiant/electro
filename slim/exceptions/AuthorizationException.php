<?php
namespace Electro\exceptions;

use \Exception;

class AuthorizationException extends Exception {
	protected $code = 403;
	protected $message = "You are not authorized to perform this action";

	public function __construct() {}
}