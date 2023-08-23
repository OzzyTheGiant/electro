<?php
namespace Electro\Controllers;

use Electro\Exceptions\DatabaseException;
use \Exception;
use Illuminate\Database\Query\Builder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;


class BillController {
    public static string $table_name = "bills";

	public function __construct(
        protected Builder $table
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

	public function add(Request $request, Response $response): Response {
        $bill = $request->getParsedBody();

        if (!$bill || count($bill) == 0) throw new HttpBadRequestException(
            $request, 
            "Empty request body"
        );

		try {
			$this->table->insert($bill);
		} catch (Exception $e) {
            throw new DatabaseException($request, $e->getMessage());
		}
        
        $response->getBody()->write(json_encode($bill));
        return $response->withStatus(201)->withHeader("Content-Type", "application/json");
	}

	public function update(Request $request, Response $response, array $args): Response {
        $bill = $request->getParsedBody();

		if (!$bill || count($bill) == 0) throw new HttpBadRequestException(
            $request,
            "Empty request body"
        );

		try {
			$affected = $this->table->where("id", $args["id"] ?? $bill["id"])->update($bill);
		} catch (Exception $e) {
            throw new DatabaseException($request, $e->getMessage());
		}

        if (!$affected) throw new HttpNotFoundException($request, "Bill does not exist");

        $response->getBody()->write(json_encode($bill));
        return $response->withHeader("Content-Type", "application/json");
	}

	/** deletes record from database table */
	public function delete(Request $request, Response $response, array $args): Response {
		try {
			$deleted = $this->table->where("id", (int) $args["id"])->delete();
		} catch (Exception $e) {
            throw new DatabaseException($request, $e->getMessage());
		}

        if (!$deleted) throw new HttpNotFoundException($request, "Bill does not exist");

        return $response->withStatus(204);
	}
}
