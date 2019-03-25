<?php
namespace Electro\services;

use Electro\exceptions\ValidationException;
use \DateTime;

class Validators {
	public const REQUIRED = "required";
	public const MAX = "max";
	public const IS_DATE = "isDate";

	public static function required(string $field_name, $value) {
		if (empty($value) || floatVal($value) == 0) {
			throw new ValidationException($field_name, ValidationException::TYPES[self::REQUIRED]);
		}
	}

	public static function max($max_value) {
		return function(string $field_name, $value) use ($max_value) {
			if (is_string($value) && strlen($value) > $max_value) {
				throw new ValidationException($field_name, ValidationException::TYPES["max_string_size"]);
			} else if (is_numeric($value) && $value > $max_value) {
				throw new ValidationException($field_name, ValidationException::TYPES["max_number_size"]);
			}
		};
	}

	public static function isDate(string $field_name, $value) {
		$date = DateTime::createFromFormat("Y-m-d", $value);
		if (!($date && $date->format('Y-m-d') === $value)) {
			throw new ValidationException($field_name, ValidationException::TYPES["is_date"]);
		}
	}
}