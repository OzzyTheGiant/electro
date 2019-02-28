<?php
namespace Electro\services;

use \Psr\Container\ContainerInterface;

class CSRFTokenManager {
	protected $container;

	public function __construct($container) {
		$this->container = $container;
	}

	/** get csrf token saved in session */
	public function getToken() {
		$session = $this->container->session;
		return $session->getSegment($session->getName())->get('csrf_token');
	}

	/** generate a new token, save to session, and set its cookie */
	public function generateToken() {
		$session = $this->container->session;
		$keys = array_merge(range(0,9), range('a', 'z'));
		$csrf_token = "";
		for($i=0; $i < 16; $i++) {
			$csrf_token .= $keys[mt_rand(0, count($keys) - 1)];
		}
		$session->getSegment($session->getName())->set("csrf_token", password_hash($csrf_token, PASSWORD_BCRYPT));
		$this->setCSRFCookie($csrf_token);
	}

	/** set csrf token cookie if token provided, otherwise delete cookie */
	public function setCSRFCookie($token = null) {
		setcookie("XSRF-TOKEN", $token, $token ? strtotime('1 hour') : time() - 3600, "/", "", ENVIRONMENT === "production", false);
	}
}