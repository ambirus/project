<?php

namespace lib\db;

use lib\Config;
use PDO;
use Exception;

class Mysql
{
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
     * @return PDO
     * @throws Exception
     */
    public static function getConnection()
    {
        if (self::$connection === null) {
            $dbConfig = Config::get('db');
            self::$connection = new PDO(
                'mysql:host=' . $dbConfig['host'] . ';dbname=' . $dbConfig['database'],
                $dbConfig['login'],
                $dbConfig['password'],
                [
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",
                    PDO::ATTR_TIMEOUT => 55,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
        }

        return self::$connection;
    }
}