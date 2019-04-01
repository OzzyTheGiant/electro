<?php
namespace Electro\controllers;

use \TypeError;
use \Exception;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Electro\exceptions\NotFoundException;
use Electro\exceptions\DatabaseException;
use Electro\exceptions\ValidationException;
use Electro\exceptions\EmptyRequestBodyException;

class ModelController {
	/** @var ContainerInterface $container */
	protected $container;
	/** @var String $entity_name */
	protected $entity_name = "";
	/** @var String $table_name */
	protected $table_name = "";

	public function __construct(ContainerInterface $container) {
		$this->container = $container;
	}

	/** @return array returns array of query results, or empty array if no results found */
	public function getAll(Request $request, Response $response, $order_by = null): Response {
		try {
			$query = $this->container->atlas->get($this->table_name)->select();
			if ($order_by) $query = $query->orderBy($order_by);
			$results = $query->fetchRows();
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
		} catch (TypeError $e) {
			throw new EmptyRequestBodyException();
		} catch (ValidationException $e) {
			throw $e;
		} catch (Exception $e) {
			throw new DatabaseException($e->getMessage());
		}
	}

	/** updates record in database table */
	public function update(Request $request, Response $response, array $args): response {
		if (!$bill = $request->getParsedBody()) throw new EmptyRequestBodyException();
		try {
			$table = $this->container->atlas->get($this->table_name);
			$row = $table->fetchRow($args["id"]);
			foreach ($bill as $key => $value) {
				$row->{$key} = $value;
			}
			$table->updateRow($row);
			return $response->withJson($row);
		} catch (TypeError $e) {
			if (!$row) throw new NotFoundException($this->entity_name);
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
			if (!$row) throw new NotFoundException($this->entity_name);
		} catch (Exception $e) {
			throw new DatabaseException($e->getMessage());
		}
	}
}