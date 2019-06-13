<?php
namespace Electro\exceptions;

use \Exception;

class NotFoundException extends Exception {
	protected $code = 404;

	public function __construct($item) {
		$this->message = "The specified " . ($item ?: "item") . " could not be found";
	}
}