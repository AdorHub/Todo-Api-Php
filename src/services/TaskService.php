<?php

namespace App\Services;

use App\Exceptions\DatabaseException;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Models\Task;
use App\Traits\ValidateInput;
use App\Repositories\TaskRepository;

class TaskService
{
	use ValidateInput;

	public function __construct(private TaskRepository $repo) {}

	public function getAll(): array
	{
		$tasks = $this->repo->getAll();
		if (!$tasks) {
			throw new ValidationException('There is no tasks yet, make one');
		}
		return $tasks;
	}

	public function get(int $id): array
	{
		$id = $this->clearID($id);
		if (!$id) {
			throw new ValidationException('Invalid resource ID');
		}
		$task = $this->repo->get($id);
		if (!$task) {
			throw new NotFoundException('Task not found');
		}
		return $task;
	}

	public function create(array $data): array
	{
		$errors = [];
		$data = $this->clearInputs($data);
		$missingFields = $this->getMissingFields(['title', 'status'], $data);
		if (!empty($missingFields)) {
			$errors[] = 'Missing required field: ' . implode(', ', $missingFields);
			throw new ValidationException($errors);
		}
		if (!$this->matchOnlyOneValue($data['status'] ?? '', Task::ALLOWED_STATUSES)) {
			$errors[] = 'Status field must be ' . implode(' OR ', Task::ALLOWED_STATUSES);
		}
		if (!$this->checkStringSize($data['title'] ?? '', 6, 255)) {
			$errors[] = 'Title field size must be between 6 and 255';
		}
		if (!$this->checkStringSize($data['description'] ?? '', 0, 500)) {
			$errors[] = 'Description field max size is 500';
		}
		if (!empty($errors)) {
			throw new ValidationException($errors);
		}
		$task = new Task(null, $data['title'], $data['description'] ?? '', $data['status']);
		$task = $this->repo->create($task);
		if (!$task) {
			throw new DatabaseException('Failed on creating task');
		}
		return $task;
	}

	public function update(int $id, array $data): array
	{
		$errors = [];
		$data = $this->clearInputs($data);
		$missingFields = $this->getMissingFields(['title', 'status'], $data);
		if (!empty($missingFields)) {
			$errors[] = 'Missing required field: ' . implode(', ', $missingFields);
			throw new ValidationException($errors);
		}
		if (!$this->matchOnlyOneValue($data['status'], Task::ALLOWED_STATUSES)) {
			$errors[] = 'Status field must be ' . implode(' OR ', Task::ALLOWED_STATUSES);
		}
		if (!$this->checkStringSize($data['title'], 6, 255)) {
			$errors[] = 'Title field size must be between 6 and 255';
		}
		if (!$this->checkStringSize($data['description'], 0, 500)) {
			$errors[] = 'Description field max size is 500';
		}
		if (!empty($errors)) {
			throw new ValidationException($errors);
		}
		$task = $this->get($id);
		$task = Task::fromArray($task);
		$task = $this->repo->update($task);
		if (!$task) {
			throw new DatabaseException('Failed on updating task');
		}
		return $task;
	}

	public function delete(int $id): void
	{
		$result = $this->repo->delete($id);
		if (!$result) {
			throw new DatabaseException('Failed on deleting task');
		}
	}
}