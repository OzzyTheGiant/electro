<?php
if ($_SERVER["REQUEST_URI"] === "application.php") exit;

require_once("vendor/autoload.php");
use Psr\Container\ContainerInterface;
use electro\controllers\ModelController;
use Atlas\Table\TableLocator;
use Psr\Http\Message\RequestInterface as Request;

$env = "ENVIRONMENT"; // ignore intelephense duplicate symbol error
if ($_SERVER["SERVER_NAME"] === "electro") {
	define("$env", "development");
} else {
	define("$env", "production");
}

// Create app with configuration settings
$app = new Slim\App(include_once("config/slim-config.php"));
$container = $app->getContainer();

/* === CONTAINER DEPENDENCIES === */
$container["errorHandler"] = function(ContainerInterface $container) {
	return function(Request $request, Response $response, $exception) {
		return $response
			->withStatus(500)
			->withJson('{"message":"Server Error: ' . $exception->getMessage());
	};
};

$container["atlas"] = function(ContainerInterface $container) {
	$args = $container['settings']['atlas']['pdo'];
	$database = TableLocator::new(...$args); // $args are PDO() parameters
	return $database;
};

/* === ROUTES === */
$app->group("/api", function() {
	$this->group("", function() {
		$this->get("/bills", ModelController::class . ":getAll");
		$this->post("/bill", ModelController::class . ":add");
		$this->put("/bill", ModelController::class .":update");
		$this->delete("/bill/{id}", ModelController::class . ":delete");
	});
});

$app->run();
?>