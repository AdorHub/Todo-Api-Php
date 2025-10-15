<?php

namespace App\Repositories;

use PDO;
use App\Models\Task;

class TaskRepository
{
	public function __construct(private PDO $pdo) {}

	public function getAll(): ?array
	{
		$sql = "SELECT * FROM tasks ORDER BY id DESC;";
		$stmt = $this->pdo->query($sql);
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);	
		return empty($result) ? null : $result;
	}

	public function get(int $id): ?array
	{
		$sql = "SELECT * FROM tasks WHERE id = :id;";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute([
			'id' => $id
		]);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result ? $result : null;
	}

	public function create(Task $task): ?array
	{
		$sql = "INSERT INTO tasks (title, description, status) VALUES (:title, :description, :status);";
		$stmt = $this->pdo->prepare($sql);
		$result = $stmt->execute([
			'title' => $task->getTitle(),
			'description' => $task->getDescription(),
			'status' => $task->getStatus()
		]);
		if ($result && $stmt->rowCount() > 0) {
			$task = $task->setId((int) $this->pdo->lastInsertId());
			return Task::toArray($task);
		}
		return null;
	}

	public function update(Task $task): ?array
	{
		$sql = "UPDATE tasks SET title = :title, description = :description, status = :status WHERE id = :id;";
		$stmt = $this->pdo->prepare($sql);
		$result = $stmt->execute([
			'id' => $task->getId(),
			'title' => $task->getTitle(),
			'description' => $task->getDescription(),
			'status' => $task->getStatus()
		]);
		if ($result && $stmt->rowCount() > 0) {
			return Task::toArray($task);
		}
		return null;
	}

	public function delete(int $id): bool
	{
		$sql = "DELETE FROM tasks WHERE id = :id;";
		$stmt = $this->pdo->prepare($sql);
		$result = $stmt->execute([
			'id' => $id
		]);
		if ($result && $stmt->rowCount() > 0) {
			return true;
		}
		return false;
	}
}