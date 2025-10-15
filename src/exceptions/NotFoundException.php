<?php

namespace App\Exceptions;

use Exception;

class NotFoundException extends Exception
{
	public function __construct(private array|string $errors)
	{
		parent::__construct('Resource not found');
	}

	public function getErrors(): array|string
	{
		return $this->errors;
	}
}
