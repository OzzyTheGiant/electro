<?php
namespace Electro\controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Electro\models\User\UserTable;
use Electro\models\User\UserRow;
use Electro\exceptions\AuthenticationException;

class LoginController {
	/** @var ContainerInterface $container */
	private $container;
	/** @var String $table_name */
	private $table_name = UserTable::class;

	private const SESSION_SEGMENT = "Electro";

	public function __construct($container) {
		$this->container = $container;
	}

	public function home(Request $request, Response $response): Response {
		return $response->withStatus(204);
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
				$current_user = ["ID" => $user->ID, "Username" => $user->Username];
				$this->startLoginSession($current_user);
				return $response->withStatus(200)->withJson($current_user);
			} else {
				throw new AuthenticationException();
			}
		} throw new AuthenticationException();
	}

	/** save current user in session segment */
	private function startLoginSession(array $user): void {
		$session = $this->container->session;
		$session->regenerateId();
		$segment = $session->getSegment(self::SESSION_SEGMENT);
		$segment->set("CurrentUser", $user);
	}

	/** dispose of session and create a new one to log back in */
	public function logout(Request $request, Response $response): Response {
		$this->container->session->regenerateId();
		return $response->withStatus(204);
	}
}