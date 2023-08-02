<?php
namespace Electro\Config;

use DI\Container;
use Electro\Controllers\BillController;
use Electro\exceptions\Loggable;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container as DBContainer;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface as Request;
use Slim\App;
use Slim\Exception\HttpSpecializedException;
use Throwable;

class DependencyManager {
    public static function bootstrapDependencies(): ContainerInterface {
        $container = new Container();
        $container->set("settings", require_once("settings.php"));
        $container->set("logger", self::createLogger());
        $container->set("database", self::createDatabase($container));
        $container->set(BillController::class, self::createBillController($container));
        return $container;
    }

    public static function setUpErrorHandler(App $app): void {
        $container = $app->getContainer();

        $error_callback = function (Request $request, Throwable $error) use ($app, $container) {
            $response = $app->getResponseFactory()->createResponse();

            $container->get("logger")->error(
                $error->getMessage(), 
                $error instanceof Loggable ? $error->getMetadata() : []
            );

            $response->getBody()->write(json_encode([
                "message" => $error instanceof HttpSpecializedException && $error->getCode() != 500 ? 
                    $error->getMessage() : 
                    "Server Error: Try again or contact for assistance"
            ]));
                
            return $response
                ->withStatus($error->getStatusCode() ?: 500)
                ->withHeader("Content-Type", "application/json");
        };

        $app->addErrorMiddleware(true, true, true, $container->get("logger"))
            ->setDefaultErrorHandler($error_callback);
    }

    private static function createDatabase(ContainerInterface $container): Capsule {
        $config = $container->get("settings")["db"];
        $database = new Capsule;
        $database->addConnection($config);
        $database->setEventDispatcher(new Dispatcher(new DBContainer));
        $database->setAsGlobal();
        $database->bootEloquent();
        return $database;
    }

    private static function createLogger(): Logger {
        $logger = new Logger('ApplicationLog');
        $logger->pushHandler(new StreamHandler($_ENV["LOG_FILE_PATH"], Logger::WARNING));
        $logger->pushHandler(new StreamHandler("php://stderr", Logger::WARNING));
        return $logger;
    }

    private static function createBillController(ContainerInterface $container): BillController {
        return new BillController($container->get("database")->table(BillController::$table_name));
    }
}
