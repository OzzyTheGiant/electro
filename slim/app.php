<?php
require_once("vendor/autoload.php");

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Dotenv\Dotenv;
// use Electro\controllers\LoginController;
// use Electro\controllers\BillController;
use Electro\middleware\CSRFTokenMiddleware;

// load environment variables from .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Create app with configuration settings
$app = AppFactory::create(new ResponseFactory());
$app->addErrorMiddleware(true, true, true);

// $app->add(new CSRFTokenMiddleware($container));

/* === ROUTES === */
$app->get("/", function(Request $request, Response $response, array $args) {
    $response->getBody()->write("Hello World");
    return $response;
});
// $app->group("/api", function() {
// 	$this->get("", LoginController::class . ":home");
// 	$this->post("/login", LoginController::class . ":login");
// 	$this->post("/logout", LoginController::class . ":logout");
// 	$this->group("/bills", function() {
// 		$this->get("", BillController::class . ":getAllBills");
// 		$this->post("", BillController::class . ":add");
// 		$this->put("/{id}", BillController::class .":update");
// 		$this->delete("/{id}", BillController::class . ":delete");
// 	});
// });

$app->run();
