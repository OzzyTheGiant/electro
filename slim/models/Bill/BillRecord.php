<?php
declare(strict_types=1);

namespace electro\models\Bill;

use Atlas\Mapper\Record;

/**
 * @method BillRow getRow()
 */
class BillRecord extends Record
{
    use BillFields;
}
