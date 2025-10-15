<?php

namespace App\Exceptions;

use Exception;

class DatabaseException extends Exception
{
	public function __construct(private array|string $errors)
	{
		parent::__construct('Database action failed');
	}

	public function getErrors(): array|string
	{
		return $this->errors;
	}
}
