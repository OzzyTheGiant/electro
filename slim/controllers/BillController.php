<?php
namespace Electro\controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Electro\controllers\ModelController;
use Electro\models\Bill\BillTable;


class BillController extends ModelController {
	protected $entity_name = "bill";
	protected $table_name = BillTable::class;

	public function getAllBills(Request $request, Response $response) {
		return parent::getAll($request, $response, "PaymentDate DESC");
	}
}