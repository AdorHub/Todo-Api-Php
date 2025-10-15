<?php

namespace App;

use App\Controllers\TaskController;
use App\Http\Request;
use App\Http\Response;
use App\Interfaces\MiddlewareInterface;
use App\Traits\ValidateJson;

class Router
{
	use ValidateJson;

	private array $middlewares = [];

	public function __construct(private TaskController $controller) {}

	public function setMiddleware(MiddlewareInterface $middleware): void
	{
		$this->middlewares[] = $middleware;
	}

	public function run(): void
	{
		$request = new Request;
		$response = new Response;

		foreach ($this->middlewares as $middleware) {
			$result = $middleware->process($request, $response);
			if ($result instanceof Response) {
				$result->output();
				return;
			}
		}

		$this->dispatch($request, $response);
	}

	public function dispatch(Request $request, Response $response)
	{
		$method = $request->getMethod();
		$uri = $request->getUri();

		switch ($uri) {
			case '/tasks':
				if ($method === 'GET') {
					$response = $this->controller->index($response);
					$response->output();
				} else if ($method === 'POST') {
					$response = $this->controller->store($request, $response);
					$response->output();
				}
				break;
				
			default:
				if (preg_match('/^\/tasks\/([0-9]+)/', $uri, $matches)) {
					$id = $matches[1];
					if ($method === 'GET') {
						$response = $this->controller->show($id, $response);
						$response->output();
					} else if ($method === 'PUT') {
						$response = $this->controller->update($id, $request, $response);
						$response->output();
					} else if ($method === 'DELETE') {
						$response = $this->controller->delete($id, $response);
						$response->output();
					}
				} else {
					$response->json([
						'status' => 'failed',
						'message' => 'Unsupportable method or resource',
					], 400)->output();
				}				
		}
	}
}