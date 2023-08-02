<?php
namespace Electro\Controllers;

use Electro\Exceptions\DatabaseException;
use Electro\Exceptions\ValidationException;
use \Exception;
use Illuminate\Database\Query\Builder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use \TypeError;


class BillController {
    public static string $table_name = "bills";

	public function __construct(
        protected Builder $table,
        protected string $entity_name = "",
    ) {}

	public function fetchAll(Request $request, Response $response): Response {
		try {
			$bills = $this->table->get();
		} catch (Exception $e) {
            throw new DatabaseException($request, $e->getMessage());
		}

        $response->getBody()->write(json_encode($bills));
        return $response->withHeader("Content-Type", "application/json");
	}

	/** Adds new record to database table */
	public function add(Request $request, Response $response): Response {
		try {
			$table = $this->table->get();
			$row = $table->newRow($request->getParsedBody());
			$table->insertRow($row);
		} catch (TypeError $e) {
            throw new HttpBadRequestException($request, "Empty request body");
		} catch (ValidationException $e) {
            throw $e;
		} catch (Exception $e) {
            throw new DatabaseException($request, $e->getMessage());
		}
        
        $response->getBody()->write(json_encode($row));
        return $response->withStatus(201)->withHeader("Content-Type", "application/json");
	}

	/** updates record in database table */
	public function update(Request $request, Response $response, array $args): Response {
		if (!$bill = $request->getParsedBody()) throw new HttpBadRequestException(
            $request,
            "Empty request body"
        );

		try {
			$table = $this->table->get();
			$row = $table->fetchRow($args["id"]);
			foreach ($bill as $key => $value) {
				$row->{$key} = $value;
			}
			$table->updateRow($row);

		} catch (TypeError $e) {
            if (empty($row)) throw new HttpNotFoundException(
                $request, 
                $this->entity_name . " not found"
            );
		} catch (ValidationException $e) {
            throw $e;
		} catch (Exception $e) {
            throw new DatabaseException($request, $e->getMessage());
		}

        $response->getBody()->write(json_encode($bill));
        return $response->withHeader("Content-Type", "application/json");
	}

	/** deletes record from database table */
	public function delete(Request $request, Response $response, array $args): Response {
		try {
			$table = $this->table->get();
			$row = $table->fetchRow($args["id"]);
			$table->deleteRow($row);
		} catch (TypeError $e) {
            if (empty($row)) throw new HttpNotFoundException(
                $request, 
                $this->entity_name . " not found"
            );
		} catch (Exception $e) {
            throw new DatabaseException($request, $e->getMessage());
		}

        return $response->withStatus(204);
	}
}
