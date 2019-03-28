<?php
namespace Electro\exceptions;

use \Exception;

class DatabaseException extends Exception implements Loggable {
	protected $code = 500;
	protected $metadata = null;

	public function __construct($message) {
		$this->message = "Something went wrong while querying the database";
		$this->metadata = [
			"hidden_message" => $message
		];
	}

	public function getMetadata():array {
		return $this->metadata;
	}
}