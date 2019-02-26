<?php
require_once("vendor/autoload.php");

$env = "ENVIRONMENT"; // ignore intelephense duplicate symbol error
if ($_SERVER["SERVER_NAME"] === "electro") {
	define("$env", "development");
} else {
	define("$env", "production");
}

$app = new Slim\App();

$container = $app->getContainer();

$app->group("/api", function() {
	$this->get("/bills");
	$this->post("/bill");
	$this->put("/bill/{id}");
	$this->patch("/bill/{id}");
	$this->delete("/bill/{id}");
});

$app->run();