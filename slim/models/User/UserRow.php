<?php
/**
 * This file was generated by Atlas. Changes will be overwritten.
 */
declare(strict_types=1);

namespace Electro\models\User;

use Atlas\Table\Row;

/**
 * @property mixed $ID int(10,0) NOT NULL
 * @property mixed $Username varchar(30) NOT NULL
 * @property mixed $Password varchar(255) NOT NULL
 */
class UserRow extends Row
{
    protected $cols = [
        'ID' => null,
        'Username' => null,
        'Password' => null,
    ];
}