<?php

namespace App\Traits;

trait ValidateInput
{
	protected function clearInputs(array $data): array
	{
		return array_map(fn($item) => preg_replace('/\s+/', ' ', strip_tags(htmlspecialchars(strtolower(trim($item))))), $data); 
	}

	protected function clearID(int $id): ?int
	{
		$cleanID = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
		if ($cleanID <= 0 || !filter_var($cleanID, FILTER_VALIDATE_INT)) {
			return null;
		}
		return (int) $cleanID;
	}

	protected function getMissingFields(array $fields, array $data): array
	{
		$missingFields = [];
		foreach ($fields as $field) {
			if (!isset($data[$field])) {
				$missingFields[] = $field;
			}
		}
		return $missingFields;
	}

	protected function matchOnlyOneValue(string $input, array $values): bool
	{
		return in_array($input, $values);
	}

	protected function checkStringSize(string $input, int $min, int $max): bool
	{
		return strlen($input) > $min && strlen($input) < $max;
	}
}