<?php

namespace Project\auth;

use Project\db\Table;

/**
 * Class User.
 */
class User extends Table
{
    /**
     * @var string
     */
    protected $name = 'users';

    /**
     * @var bool
     */
    protected $softDelete = true;
}
