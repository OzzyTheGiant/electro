<?php
namespace Electro\Exceptions;

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpInternalServerErrorException;

class DatabaseException extends HttpInternalServerErrorException implements Loggable {
	protected $metadata = null;

	public function __construct(Request $request, string $message) {
        parent::__construct($request, $message);
		$this->metadata = ["hidden_message" => $message];
	}

	public function getMetadata(): array {
		return $this->metadata;
	}

    public function getStatusCode(): int {
        return $this->code;
    }
}