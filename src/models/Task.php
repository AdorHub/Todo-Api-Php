<?php

namespace App\Models;

class Task
{
	public const ALLOWED_STATUSES = ['finished', 'unfinished'];

	public function __construct(private ?int $id, private string $title, private ?string $description, private string $status) {}

	public function getId(): int
	{
		return $this->id;
	}

	public function getTitle(): string
	{
		return $this->title;
	}

	public function getDescription(): string|null
	{
		return $this->description ?? null;
	}

	public function getStatus(): string
	{
		return $this->status;
	}

	public function setId(int $id): self
	{
		$this->id = $id;
		return $this;
	}

	public function setTitle(string $title): self
	{
		$this->title = $title;
		return $this;
	}

	public function setDescription(?string $description): self
	{
		$this->description = $description;
		return $this;
	}

	public static function fromArray(array $data): Task
	{
		return new Task($data['id'], $data['title'], $data['description'] ?? null, $data['status']);
	}

	public static function toArray(Task $task): array
	{
		return [
			'id' => $task->getId(),
			'title' => $task->getTitle(),
			'description' => $task->getDescription(),
			'status' => $task->getStatus()
		];
	}
}