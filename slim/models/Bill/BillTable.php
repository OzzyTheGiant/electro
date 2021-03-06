<?php
/**
 * This file was generated by Atlas. Changes will be overwritten.
 */
declare(strict_types=1);

namespace Electro\models\Bill;

use Atlas\Table\Table;

/**
 * @method BillRow|null fetchRow($primaryVal)
 * @method BillRow[] fetchRows(array $primaryVals)
 * @method BillTableSelect select(array $whereEquals = [])
 * @method BillRow newRow(array $cols = [])
 * @method BillRow newSelectedRow(array $cols)
 */
class BillTable extends Table
{
    const DRIVER = 'mysql';

    const NAME = 'Bills';

    const COLUMNS = [
        'ID' => [
            'name' => 'ID',
            'type' => 'int',
            'size' => 10,
            'scale' => 0,
            'notnull' => true,
            'default' => null,
            'autoinc' => true,
            'primary' => true,
            'options' => null,
        ],
        'PaymentAmount' => [
            'name' => 'PaymentAmount',
            'type' => 'decimal',
            'size' => 5,
            'scale' => 2,
            'notnull' => true,
            'default' => '0.00',
            'autoinc' => false,
            'primary' => false,
            'options' => null,
        ],
        'PaymentDate' => [
            'name' => 'PaymentDate',
            'type' => 'date',
            'size' => null,
            'scale' => null,
            'notnull' => true,
            'default' => null,
            'autoinc' => false,
            'primary' => false,
            'options' => null,
        ],
        'User' => [
            'name' => 'User',
            'type' => 'int',
            'size' => 10,
            'scale' => 0,
            'notnull' => true,
            'default' => null,
            'autoinc' => false,
            'primary' => false,
            'options' => null,
        ],
    ];

    const COLUMN_NAMES = [
        'ID',
        'PaymentAmount',
        'PaymentDate',
        'User',
    ];

    const COLUMN_DEFAULTS = [
        'ID' => null,
        'PaymentAmount' => '0.00',
        'PaymentDate' => null,
        'User' => null,
    ];

    const PRIMARY_KEY = [
        'ID',
    ];

    const AUTOINC_COLUMN = 'ID';

    const AUTOINC_SEQUENCE = null;
}
