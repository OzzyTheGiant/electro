<?php
namespace Electro\exceptions;

use \Exception;

class ValidationException extends Exception {
	protected $code = 400;
	protected $message = "The data provided is invalid";

	public const TYPES = [
		"required" => 0,
		"is_date" => 1,
		"max_number_size" => 2,
		"max_string_size" => 3
	];

	public function __construct(string $property, int $validation_type) {
		switch($validation_type) {
			case self::TYPES["required"]: $this->message = "$property is required"; break;
			case self::TYPES["is_date"]: $this->message = "$property is not a valid date"; break;
			case self::TYPES["max_number_size"]: $this->message = "$property must be between $0 and $99,999.99"; break;
			case self::TYPES["max_string_size"]: $this->message = "$property is too large"; break;
		}
	}
}