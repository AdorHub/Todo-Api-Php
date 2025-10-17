<?php

use App\Router;
use Dotenv\Dotenv;
use Pimple\Container;
use App\Helpers\Logger;
use App\Services\TaskService;
use App\Controllers\TaskController;
use App\Helpers\Handler;
use App\Middlewares\JsonValidationMiddleware;
use App\Repositories\TaskRepository;

require __DIR__ . '/vendor/autoload.php';

header('Content-Type: application/json');

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$container = new Container();
$container['logger'] = function () {
	$logFilePath = realpath(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $_ENV['LOG_PATH']);
	$logDir = dirname($logFilePath);
	if (!is_dir($logDir)) {
		mkdir($logDir, 0755, true);
	}
	return new Logger($logFilePath);
};
$container['handler'] = function ($c) {
	return new Handler($c['logger']);
};

$handler = $container['handler'];
$handler->registerExceptionHandler();
$handler->registerErrorHandler();
$handler->registerFatalErrorHandler();

$container['database'] = function ($c) {
	$logger = $c['logger'];
	switch ($_ENV['DB_DRIVER']) {
		case 'sqlite':
			$dbFilePath = str_replace('\\', '/', realpath(__DIR__)) . '/' . $_ENV['DB_PATH'];
			$dbDir = dirname($dbFilePath);
			if (!is_dir($dbDir)) {
				mkdir($dbDir, 0755, true);
			}
			try {
				return new PDO("sqlite:$dbFilePath");
			} catch (PDOException $e) {
				$logger->error($e->getMessage(), $e->getFile(), $e->getLine(), 'exception');
				throw new Exception("Error connecting to SQLite database");
			}
			break;

		case 'mysql':
			$dsn = sprintf(
				'%s:host=%s;dbname=%s',
				$_ENV['DB_DRIVER'],
				$_ENV['DB_HOST'],
				$_ENV['DB_NAME']
			);
			try {
				return new PDO($dsn, $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);
			} catch (PDOException $e) {
				$logger->error($e->getMessage(), $e->getFile(), $e->getLine(), 'exception');
				throw new Exception('Error connecting to MYSQL database');
			}			
			break;
			
		default:
			throw new Exception('Unsupported database driver');
	}
};
$container['task_repository'] = function ($c) {
	return new TaskRepository($c['database']);
};
$container['task_service'] = function ($c) {
	return new TaskService($c['task_repository']);
};
$container['task_controller'] = function ($c) {
	return new TaskController($c['task_service']);
};
$container['router'] = function ($c) {
	$router =  new Router($c['task_controller']);
	$router->setMiddleware(new JsonValidationMiddleware);
	return $router;
};

$router = $container['router'];
$router->run();