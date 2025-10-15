<?php

namespace App\Helpers;

class Logger
{
	public function __construct(private string $logFilePath) {}

	const DEBUG = 100;
	const INFO = 200;
	const WARNING = 300;
	const ERROR = 400;

	public function log(int $level, string $text, string $file, string $line, string $type)
	{
		switch ($level) {
			case self::DEBUG:
				$prefix = '[DEBUG]';
				break;
			case self::INFO:
				$prefix = '[INFO]';
				break;
			case self::WARNING:
				$prefix = '[WARNING]';
				break;
			case self::ERROR:
				$prefix = '[ERROR]';
				break;
			default:
				$prefix = '[UNKNOWN]';
		}
		$timestamp = date('Y-m-d H:i:s');
		$type = '{' . strtoupper($type) . '}';
		$logText = "$prefix $type $timestamp $text in $file:$line";
		file_put_contents($this->logFilePath, $logText . "\n", FILE_APPEND);
	}

	public function debug(string $text, string $file, string $line, string $type)
	{
		$this->log(self::DEBUG, $text, $file, $line, $type);
	}

	public function info(string $text, string $file, string $line, string $type)
	{
		$this->log(self::INFO, $text, $file, $line,  $type);
	}

	public function warning(string $text, string $file, string $line, string $type)
	{
		$this->log(self::WARNING, $text, $file, $line, $type);
	}

	public function error(string $text, string $file, string $line, string $type)
	{
		$this->log(self::ERROR, $text, $file, $line, $type);
	}
}