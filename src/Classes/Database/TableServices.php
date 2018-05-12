<?php
/**
 * Created by PhpStorm.
 * User: stasich
 * Date: 09.05.18
 * Time: 2:53
 */

namespace Classes\Database;

use PDO;

class TableServices {
    private static $instance = NULL;

    private $pdo;

    private function __construct()
    {
        $this->pdo = DbConnection::getConnection();
        $this->pdo->exec(
            'CREATE TABLE IF NOT EXISTS services (id INTEGER PRIMARY KEY AUTOINCREMENT, service_prefix TEXT)'
        );
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    /**
     * @return TableServices
     */
    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new TableServices();
        }

        return self::$instance;
    }

    /**
     * @param string $prefix
     * @throws \Exception
     * @return integer
     */
    public function getServiceIdByPrefix($prefix) {
        $service_id = $this->getIdByPrefix($prefix);

        if (!$service_id) {
            $this->addPrefix($prefix);
        }

        $service_id = $this->getIdByPrefix($prefix);

        if (!$service_id) {
            throw new \Exception('Can\t get/add prefix');
        }

        return (int)$service_id;
    }

    /**
     * @param string $prefix
     */
    private function addPrefix($prefix) {
        if (!empty($prefix)) {
            $query = 'INSERT INTO services (service_prefix) values (:prefix)';
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([
                'prefix' => $prefix,
            ]);

        }
    }

    /**
     * @param string $prefix
     * @return integer|bool
     */
    private function getIdByPrefix($prefix) {
        $query = 'SELECT id FROM services where service_prefix = :prefix';
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'prefix' => $prefix,
        ]);
        $service_id = $stmt->fetch(PDO::FETCH_COLUMN);

        return $service_id;
    }
}
