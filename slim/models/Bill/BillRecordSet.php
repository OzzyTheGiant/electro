<?php
declare(strict_types=1);

namespace electro\models\Bill;

use Atlas\Mapper\RecordSet;

/**
 * @method BillRecord offsetGet($offset)
 * @method BillRecord appendNew(array $fields = [])
 * @method BillRecord|null getOneBy(array $whereEquals)
 * @method BillRecordSet getAllBy(array $whereEquals)
 * @method BillRecord|null detachOneBy(array $whereEquals)
 * @method BillRecordSet detachAllBy(array $whereEquals)
 * @method BillRecordSet detachAll()
 * @method BillRecordSet detachDeleted()
 */
class BillRecordSet extends RecordSet
{
}
