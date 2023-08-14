<?php
namespace Electro\Exceptions;

interface Loggable {
    public function getMessage(): string;
	public function getMetadata(): array;
    public function getStatusCode(): int;
}
