<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Services\TaskService;

class TaskController
{
	public function __construct(private TaskService $service) {}

	public function index(Response $response): Response
	{
		$tasks = $this->service->getAll();
		return $response->json([
			'status' => 'success',
			'message' => 'Success on fetching task',
			'data' => $tasks
		]);
	}

	public function store(Request $request, Response $response): Response
	{
		$data = json_decode($request->getBody(), true);
		$task = $this->service->create($data);
		return $response->json([
			'status' => 'success',
			'message' => 'Success on creating task',
			'data' => $task
		], 201);
	}

	public function show(int $id, Response $response): Response
	{
		$task = $this->service->get($id);
		return $response->json([
			'status' => 'success',
			'message' => 'Success on fetching task',
			'data' => $task
		]);
	}

	public function update(int $id, Request $request, Response $response): Response
	{
		$data = json_decode($request->getBody(), true);
		$task = $this->service->update($id, $data);
		return $response->json([
			'status' => 'success',
			'message' => 'Success on updating task',
			'data' => $task
		]);
	}

	public function delete(int $id, Response $response): Response
	{
		$this->service->delete($id);
		return $response->json([
			'status' => 'success',
			'message' => 'Success on deleting task'
		], 204);
	}
}