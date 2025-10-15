<?php

namespace App\Http;

class Response
{
	private int $statusCode = 200;
	private string $body = '';

	public function withStatus(int $code): self
	{
		$this->statusCode = $code;
		return $this;
	}

	public function output(): void
	{
		http_response_code($this->statusCode);
		echo $this->body;
	}

	public function json(array $data, int $code = 200): self
	{
		
		$this->withStatus($code);
		$this->body = json_encode($data);
		header('Content-Type: application-json');
		return $this;
	}
}