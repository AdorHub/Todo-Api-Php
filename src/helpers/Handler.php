<?php

namespace App\Helpers;

use App\Exceptions\DatabaseException;
use App\Helpers\Logger;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;

class Handler
{
	public function __construct(private Logger $logger) {}

	public function registerExceptionHandler()
	{
		set_exception_handler([$this, 'handleException']);
	}

	public function registerErrorHandler()
	{
		set_error_handler([$this, 'handleError']);
	}

	public function registerFatalErrorHandler()
	{
		register_shutdown_function([$this, 'handleFatalError']);
	}

	public function handleException(\Throwable $th)
	{
		$this->logger->error($th->getMessage(), $th->getFile(), $th->getLine(), 'exception');

		switch (true) {
			case $th instanceof NotFoundException:
				http_response_code(404);
				break;
			case $th instanceof ValidationException:
				http_response_code(400);
				break;
			case $th instanceof DatabaseException:
				http_response_code(422);
				break;
			default:
				http_response_code(500);
		}
		$message = method_exists($th, 'getErrors') ? $th->getErrors() : 'Something went wrong';
		header('Content-Type: application/json');
		echo json_encode([
			'status' => 'failed',
			'message' => $message
		]);
		die;
	}

	public function handleError($errno, $errstr, $errfile, $errline)
	{
		switch ($errno) {
			case E_USER_ERROR:
				$this->logger->error($errstr, $errfile, $errline, 'error');
				break;
			case E_WARNING:
				$this->logger->warning($errstr, $errfile, $errline, 'error');
				break;
			case E_NOTICE:
				$this->logger->debug($errstr, $errfile, $errline, 'error');
				break;
			default:
				$this->logger->info($errstr, $errfile, $errline, 'error');
		}

		header('Content-Type: application/json');
		http_response_code(500);
		echo json_encode([
			'status' => 'failed',
			'message' => 'Error occured'
		]);
		die;
	}

	public function handleFatalError()
	{
		$lastError = error_get_last();
		if ($lastError !== null) {
			$this->logger->error($lastError['message'], $lastError['file'], $lastError['line'], 'fatal error');
			header('Content-Type: application/json');
			http_response_code(500);
			echo json_encode([
				'status' => 'failed',
				'message' => 'Fatal error occured'
			]);
			die;
		}
	}
}