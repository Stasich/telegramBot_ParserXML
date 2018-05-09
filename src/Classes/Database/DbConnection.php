<?php
/**
 * Created by PhpStorm.
 * User: stasich
 * Date: 07.05.18
 * Time: 1:40
 */

namespace Classes\Database;

use \Classes\Config;
use \PDO;

class DbConnection {

    private static $instance = NULL;
    private $connection;

    private function __construct()
    {
        $file = Config::PATH_TO_DB_FILE. 'db.sqlite';

        if (!file_exists($file)) {
            $fp = fopen($file, 'w');
            fclose($fp);
        }

        $this->connection = new PDO('sqlite:' . $file);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    /**
     * @return \PDO
     */
    public static function getConnection() {
        if (is_null(self::$instance)) {
            self::$instance = new DbConnection();
        }

        return self::$instance->connection;
    }
}
