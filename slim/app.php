<?php
use Electro\Middleware\JWTRequiredMiddleware;
require_once("vendor/autoload.php");

use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Psr7\Factory\ResponseFactory;
use Dotenv\Dotenv;
use Electro\Config\DependencyManager;
use Electro\Controllers\BillController;
use Electro\Controllers\LoginController;
// use Electro\middleware\CSRFTokenMiddleware;

// load environment variables from .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$app = AppFactory::create(new ResponseFactory(), DependencyManager::bootstrapDependencies());
$app->addBodyParsingMiddleware();
DependencyManager::setUpErrorHandler($app);

// $app->add(new CSRFTokenMiddleware($container));

$app->group("/api", function(RouteCollectorProxy $group) {
	$group->get("/", LoginController::class . ":home");
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
