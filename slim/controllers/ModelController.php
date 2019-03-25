<?php
namespace Electro\controllers;

use \TypeError;
use \Exception;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Electro\models\Bill\BillTable;
use Electro\exceptions\NotFoundException;
use Electro\exceptions\DatabaseException;
use Electro\exceptions\ValidationException;

class ModelController {
	/** @var ContainerInterface $container */
	private $container;
	/** @var String $table_name */
	private $table_name = BillTable::class;

	public function __construct(ContainerInterface $container) {
		$this->container = $container;
	}

	/** @return array returns array of query results, or empty array if no results found */
	public function getAll(Request $request, Response $response): Response {
		try {
			$results = $this->container->atlas
				->get($this->table_name)
				->select()
				->orderBy("PaymentDate DESC")
				->fetchRows();
			return $response->withJson($results);
		} catch (Exception $e) {
			throw new DatabaseException($e->getMessage());
		}
	}

	/** Adds new record to database table */
	public function add(Request $request, Response $response): response {
		try {
			$table = $this->container->atlas->get($this->table_name);
			$row = $table->newRow($request->getParsedBody());
			$table->insertRow($row);
			return $response->withStatus(201)->withJson($row);
		} catch (ValidationException $e) {
			throw $e;
		} catch (Exception $e) {
			throw new DatabaseException($e->getMessage());
		}
	}

	/** updates record in database table */
	public function update(Request $request, Response $response, array $args): response {
		try {
			$bill = $request->getParsedBody();
			$table = $this->container->atlas->get($this->table_name);
			$row = $table->fetchRow($args["id"]);
			foreach ($bill as $key => $value) {
				$row->{$key} = $value;
			}
			$table->updateRow($row);
			return $response->withJson($row);
		} catch (TypeError $e) {
			if (!$row) throw new NotFoundException("bill");
		} catch (ValidationException $e) {
			throw $e;
		} catch (Exception $e) {
			throw new DatabaseException($e->getMessage());
		}
	}

	/** deletes record from database table */
	public function delete(Request $request, Response $response, array $args): response {
		try {
			$table = $this->container->atlas->get($this->table_name);
			$row = $table->fetchRow($args["id"]);
			$table->deleteRow($row);
			return $response->withStatus(204);
		} catch (TypeError $e) {
			if (!$row) throw new NotFoundException("bill");
		} catch (Exception $e) {
			throw new DatabaseException($e->getMessage());
		}
	}
}