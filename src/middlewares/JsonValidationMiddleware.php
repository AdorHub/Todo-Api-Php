<?php

namespace App\Middlewares;

use App\Http\Request;
use App\Http\Response;
use App\Interfaces\MiddlewareInterface;

class JsonValidationMiddleware implements MiddlewareInterface
{
	public function process(Request $request, Response $response): ?Response
	{
		if (!in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'])) {
			return null;
		}

		$rawBody = $request->getBody();
		$data = json_decode($rawBody, true);

		if (json_last_error() !== JSON_ERROR_NONE || empty($data)) {
			return $response->json([
				'status' => 'failed',
				'message' => 'Invalid JSON format of data'
			], 400);
		}
		return null;
	}
}