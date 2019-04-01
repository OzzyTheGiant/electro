<?php 
namespace Electro\exceptions;

use \Exception;

class EmptyRequestBodyException extends Exception {
	protected $message = "No data was submitted to server";
	protected $code = 400;

	public function __construct() {}
}