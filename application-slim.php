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
use Electro\controllers\LoginController;
use Electro\controllers\BillController;
use Electro\middleware\CSRFTokenMiddleware;
use Electro\middleware\SessionMiddleware;

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
	$session->setCookieParams([
		'lifetime' => $_ENV["SESSION_LIFETIME"] * 60,
		'secure' => $_ENV['APP_ENV'] !== "local",
		'httponly' => true,
		'path' => '/'
	]);
	// create brand new session, or if session cookie exists, resume previous session
	$session->start();
	return $session;
};

/* === MIDDLEWARE === */
$app->add(new SessionMiddleware($container)); // set the actual session cookie
$app->add(new CSRFTokenMiddleware($container));

/* === ROUTES === */
$app->group("/api", function() {
	$this->get("", LoginController::class . ":home");
	$this->post("/login", LoginController::class . ":login");
	$this->post("/logout", LoginController::class . ":logout");
	$this->group("/bills", function() {
		$this->get("", BillController::class . ":getAllBills");
		$this->post("", BillController::class . ":add");
		$this->put("/{id}", BillController::class .":update");
		$this->delete("/{id}", BillController::class . ":delete");
	});
});

$app->run();
?>