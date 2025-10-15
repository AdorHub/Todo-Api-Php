<?php

namespace App\Traits;

trait ValidateJson
{
	protected function validateJson(string $input): array|false
	{
		$data = json_decode($input, true);
		if (json_last_error() === JSON_ERROR_NONE && !empty($data)) {
			return $data;
		}
		return false;
	}
}