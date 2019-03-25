<?php
namespace Electro\exceptions;

use \Exception;

class DatabaseException extends Exception {
	protected $code = 500;

	public function __construct($message) {
		$this->message = "Something went wrong while querying the database";
		$this->metadata = [
			"hidden_message" => $message
		];
	}
}