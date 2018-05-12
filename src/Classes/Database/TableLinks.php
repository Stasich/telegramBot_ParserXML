<?php
/**
 * Created by PhpStorm.
 * User: stasich
 * Date: 09.05.18
 * Time: 2:53
 */

namespace Classes\Database;

use PDO;

class TableLinks {
    private static $instance = NULL;

    private $pdo;
    private $rows_limit = 100;

    private function __construct()
    {
        $this->pdo = DbConnection::getConnection();
        $this->pdo->exec(
            'CREATE TABLE IF NOT EXISTS links (id INTEGER PRIMARY KEY AUTOINCREMENT, link TEXT, service_id INTEGER)'
        );
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    /**
     * @return TableLinks
     */
    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new TableLinks();
        }

        return self::$instance;
    }

    /**
     * @param string $prefix
     * @return array
     */
    public function getLinks($prefix) {
        $service_id = TableServices::getInstance()->getServiceIdByPrefix($prefix);
        $query = "SELECT link, id FROM links where service_id = $service_id ORDER BY id DESC LIMIT $this->rows_limit";
        $stmt = $this->pdo->query($query);
        $links_arr = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        return $links_arr;
    }

    /**
     * @param string $prefix
     * @param array $viewedLinks
     */
    public function addViewedLinksToDb($viewedLinks, $prefix) {
        if (!empty($viewedLinks)) {
            $service_id = TableServices::getInstance()->getServiceIdByPrefix($prefix);
            $query = 'INSERT INTO links (link, service_id) values (:link, :service_id)';
            $stmt = $this->pdo->prepare($query);

            foreach ($viewedLinks as $link) {
                $stmt->execute([
                    'link' => $link,
                    'service_id' => $service_id,
                ]);
            }
        }
    }
}

