<?php
declare(strict_types=1);

namespace electro\models\Bill;

use Atlas\Mapper\Mapper;
use Atlas\Table\Row;

/**
 * @method BillTable getTable()
 * @method BillRelationships getRelationships()
 * @method BillRecord|null fetchRecord($primaryVal, array $with = [])
 * @method BillRecord|null fetchRecordBy(array $whereEquals, array $with = [])
 * @method BillRecord[] fetchRecords(array $primaryVals, array $with = [])
 * @method BillRecord[] fetchRecordsBy(array $whereEquals, array $with = [])
 * @method BillRecordSet fetchRecordSet(array $primaryVals, array $with = [])
 * @method BillRecordSet fetchRecordSetBy(array $whereEquals, array $with = [])
 * @method BillSelect select(array $whereEquals = [])
 * @method BillRecord newRecord(array $fields = [])
 * @method BillRecord[] newRecords(array $fieldSets)
 * @method BillRecordSet newRecordSet(array $records = [])
 * @method BillRecord turnRowIntoRecord(Row $row, array $with = [])
 * @method BillRecord[] turnRowsIntoRecords(array $rows, array $with = [])
 */
class Bill extends Mapper
{
}
