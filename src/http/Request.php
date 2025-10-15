<?php

namespace App\Http;

class Request
{
	private array $headers;
	private string $method;
	private string $uri;
	private string $body;

	public function __construct()
	{
		$this->headers = getallheaders();
		$this->method = $_SERVER['REQUEST_METHOD'];
		$this->uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		$this->body = file_get_contents('php://input');
	}

	public function getHeaders(): array
	{
		return $this->headers;
	}

	public function getMethod(): string
	{
		return $this->method;
	}

	public function getUri(): string
	{
		return $this->uri;
	}

	public function getBody(): string
	{
		return $this->body;
	}
}