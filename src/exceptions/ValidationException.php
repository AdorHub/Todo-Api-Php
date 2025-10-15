<?php

namespace App\Exceptions;

use Exception;

class ValidationException extends Exception
{
	public function __construct(private array|string $errors)
	{
		parent::__construct('Validation failed');
	}

	public function getErrors(): array|string
	{
		return $this->errors;
	}
}
