<?php
declare(strict_types=1);

namespace Electro\models;

use Atlas\Query\Delete;
use Atlas\Query\Insert;
use Atlas\Query\Select;
use Atlas\Query\Update;
use Atlas\Table\Row;
use Atlas\Table\Table;
use Atlas\Table\TableEvents;
use PDOStatement;

class ModelTableEvents extends TableEvents {
	function beforeUpdateRow(Table $table, Row $row): ?array {
		$row->trimAllProperties();
		$row->validateData();
		return null;
	}

	function beforeInsertRow(Table $table, Row $row): ?array {
		$row->trimAllProperties();
		$row->validateData();
		return null;
	}
}
