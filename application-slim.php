<?php
if ($_SERVER["REQUEST_URI"] === "application.php") exit;

require_once("vendor/autoload.php");
use Psr\Container\ContainerInterface;
use Atlas\Table\TableLocator;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Aura\Session\SessionFactory;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Dotenv\Dotenv;
use Electro\controllers\ModelController;
use Electro\controllers\LoginController;
use Electro\services\CSRFTokenManager;

// load environment variables from .env
$dotenv = Dotenv::create(__DIR__);
$dotenv->load();

// Create app with configuration settings
$app = new Slim\App(include_once("slim/config/slim-config.php"));
$container = $app->getContainer();

/* === CONTAINER DEPENDENCIES === */
$container["errorHandler"] = function(ContainerInterface $container) {
	return function(Request $request, Response $response, $exception) {
		return $response
			->withStatus($exception->getCode() ?: 500)
			->withJson(["message" => $exception->getMessage()]);
	};
};

$container["phpErrorHandler"] = function(ContainerInterface $container) {
	return $container->get("errorHandler"); // PHP 7+
};

$container["atlas"] = function(ContainerInterface $container) {
	$args = $container['settings']['atlas']['pdo'];
	$database = TableLocator::new(...$args); // $args are PDO() parameters
	return $database;
};

$container["session"] = function(ContainerInterface $container) {
	$session_factory = new SessionFactory();
	$session = $session_factory->newInstance($_COOKIE);
	$session->setName("electro"); // to specify which session cookie to use
	return $session;
};



$container["csrf"] = function(ContainerInterface $container) {
	return new CSRFTokenManager($container);
};

/* === ROUTES === */
$app->group("/api", function() {
	$this->post("/login", LoginController::class . ":login");
	$this->get("/logout", LoginController::class . ":logout");
	$this->group("/bills", function() {
		$this->get("", ModelController::class . ":getAll");
		$this->post("", ModelController::class . ":add");
		$this->put("/{id}", ModelController::class .":update");
		$this->delete("/{id}", ModelController::class . ":delete");
	});
});

$app->run();
?>