<?php

namespace Project\db;

use PDO;
use Exception;
use Project\App;

/**
 * Class Mysql.
 */
class Mysql
{
    /**
     * @var PDO
     */
    private static $connection;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    /**
     * @throws Exception
     *
     * @return PDO
     */
    public static function getConnection()
    {
        if (self::$connection === null) {
            $dbConfigFile = !defined('ENV_TEST') ? 'db' : 'db_test';
            $dbConfig = App::getConfig()->get($dbConfigFile);

            self::$connection = new PDO(
                'mysql:host='.$dbConfig['host'].';dbname='.$dbConfig['database'],
                $dbConfig['login'],
                $dbConfig['password'],
                [
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",
                    PDO::ATTR_TIMEOUT => 55,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        }

        return self::$connection;
    }
}
