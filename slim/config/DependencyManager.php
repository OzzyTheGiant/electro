<?php
namespace Electro\Config;

use DI\Container;
use Electro\Controllers\BillController;
use Electro\Controllers\LoginController;
use Electro\Exceptions\Loggable;
use Electro\Middleware\CSRFTokenMiddleware;
use Illuminate\Container\Container as DBContainer;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\App;
use Slim\Csrf\Guard as CSRFGuard;
use Slim\Exception\HttpSpecializedException;
use Throwable;

class DependencyManager {
    public static function bootstrapDependencies(
        ResponseFactoryInterface $response_factory
    ): ContainerInterface {
        $container = new Container();
        $container->set("settings", require_once("settings.php"));
        $container->set("logger", self::createLogger());
        $container->set("database", self::createDatabase($container));
        $container->set("csrf", new CSRFGuard($response_factory, persistentTokenMode: true));
        $container->set(BillController::class, self::createBillController($container));
        $container->set(LoginController::class, self::createLoginController($container));
        return $container;
    }

    public static function setUpErrorHandler(App $app): void {
        $container = $app->getContainer();

        $error_callback = function (Request $request, Throwable $error) use ($app, $container) {
            $response = $app->getResponseFactory()->createResponse();
            $message = "Server Error: Try again or contact for assistance";
            $is_http_error = $error instanceof HttpSpecializedException;
            $code = $error instanceof Loggable ? $error->getStatusCode() : $error->getCode();
            $code = $code ?: 500;

            if (!$is_http_error || $code >= 500) {
                $container->get("logger")->error(
                    $error->getMessage(), 
                    $error instanceof Loggable ? $error->getMetadata() : []
                );
            } else if ($is_http_error) {
                $message = $error->getMessage();
            }

            $response->getBody()->write(json_encode(["message" => $message]));
            return $response->withStatus($code)->withHeader("Content-Type", "application/json");
        };

        $app->addErrorMiddleware(true, true, true, $container->get("logger"))
            ->setDefaultErrorHandler($error_callback);
    }

    public static function setUpMiddleware(App $app): void {
        $app->add(new CSRFTokenMiddleware($app->getContainer()->get("csrf")));
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

    private static function createLoginController(ContainerInterface $container): LoginController {
        return new LoginController(
            $container->get("database")->table(LoginController::$table_name),
            $container->get("csrf")
        );
    }
    
    private static function createBillController(ContainerInterface $container): BillController {
        return new BillController($container->get("database")->table(BillController::$table_name));
    }
}
