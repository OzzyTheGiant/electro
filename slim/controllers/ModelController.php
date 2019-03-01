<?php
namespace Electro\controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Electro\models\Bill\BillTable;

class ModelController {
	/** @var ContainerInterface $container */
	private $container;
	/** @var String $table_name */
	private $table_name = BillTable::class;

	public function __construct(ContainerInterface $container) {
		$this->container = $container;
	}

	/** @return array returns array of query results, or empty array if no results found */
	public function getAll(Request $request, Response $response) {
		$results = $this->container->atlas
			->get($this->table_name)
			->select()
			->orderBy("PaymentDate DESC")
			->fetchRows();
		return $response->withJson($results);
	}

	/** Adds new record to database table */
	public function add(Request $request, Response $response) {
		$table = $this->container->atlas->get($this->table_name);
		$row = $table->newRow($request->getParsedBody());
		$table->insertRow($row);
		return $response->withStatus(201)->withJson($row);
	}

	/** updates record in database table */
	public function update(Request $request, Response $response) {
		$bill = $request->getParsedBody();
		$table = $this->container->atlas->get($this->table_name);
		$row = $table->fetchRow($bill["ID"]);
		foreach ($bill as $key => $value) {
			$row->{$key} = $value;
		}
		$table->updateRow($row);
		return $response->withJson($row);
	}

	/** deletes record from database table */
	public function delete(Request $request, Response $response, array $args) {
		$table = $this->container->atlas->get($this->table_name);
		$row = $table->fetchRow($args["id"]);
		$table->deleteRow($row);
		return $response->withStatus(204);
	}
}