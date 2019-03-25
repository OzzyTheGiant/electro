<?php
namespace Electro\exceptions;

use \Exception;

class ValidationException extends Exception {
	protected $code = 400;
	protected $message = "The data provided is invalid";

	public function __construct($message) {
		$this->message = $message;
	}
}