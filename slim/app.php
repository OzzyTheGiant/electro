<?php
require_once("vendor/autoload.php");

use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Psr7\Factory\ResponseFactory;
use Dotenv\Dotenv;
use Electro\Config\DependencyManager;
use Electro\Controllers\BillController;
use Electro\Controllers\LoginController;
use Electro\Middleware\JWTRequiredMiddleware;

// load environment variables from .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// For CSRF cookie
session_name($_ENV["JWT_CSRF_COOKIE_NAME"]);
session_start();

$response_factory = new ResponseFactory();

$app = AppFactory::create(
    $response_factory, 
    DependencyManager::bootstrapDependencies($response_factory)
);

$app->addBodyParsingMiddleware();
DependencyManager::setUpMiddleware($app);
DependencyManager::setUpErrorHandler($app);

$app->group("/api", function(RouteCollectorProxy $group) {
	$group->get("", LoginController::class . ":home");
    $group->post("/login", LoginController::class . ":login");
	$group->post("/logout", LoginController::class . ":logout");
    $group->get("/bills", BillController::class . ":fetchAll");
    $group->post("/bills", BillController::class . ":add");
    $group->put("/bills[/{id}]", BillController::class .":update");
    $group->delete("/bills/{id}", BillController::class . ":delete");
})->add(
    new JWTRequiredMiddleware(
        $app->getContainer()->get("database")->table(LoginController::$table_name)
    )
);

$app->run();
