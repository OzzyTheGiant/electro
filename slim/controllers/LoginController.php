<?php
namespace Electro\controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Electro\models\User\UserTable;
use Electro\models\User\UserRow;

class LoginController {
	/** @var ContainerInterface $container */
	private $container;
	/** @var String $table_name */
	private $table_name = UserTable::class;

	private const SESSION_SEGMENT = "Electro";

	public function __construct(ContainerInterface $container) {
		$this->container = $container;
	}

	/** Check if user exists, and if so, verify password. If successful, log in user 
	 * @return array */
	public function login(Request $request, Response $response): Response {
		$credentials = $request->getParsedBody();
		$user = $this->container->atlas
			->get($this->table_name)
			->select()
			->where("Username = ", $credentials["username"])
			->fetchRow(); // returns array instead of UserRow
		if ($user) {
			if (password_verify($credentials["password"], $user->Password)) {
				$this->startSession($user);
				return $response->withStatus(200)->withJson($user);
			} else {
				return $response->withStatus(401)->withJson('{"message": Username or password is incorrect');
			}
		} return $response->withStatus(401)->withJson('{"message":"Username or password is not correct"}');
	}

	/** start a new session by saving current user in session segment */
	private function startSession(UserRow $user) {
		$token_manager = $this->container->csrf;
		$session = $this->container->session;
		$session->setName("electro");
		$session->setCookieParams([
			'lifetime' => '28800',
			'secure' => ENVIRONMENT === "production",
			'httpOnly' => true
		]);
		$segment = $session->getSegment(self::SESSION_SEGMENT);
		$segment->set("CurrentUser", $user);
		$segment->set("CSRFToken", $token_manager->generateToken());
	}

	public function logout(Request $request, Response $response): Response {
		$this->container->csrf->setCSRFCookie();
		$this->container->session->destroy();
		return $response->withStatus(200);
	}
}