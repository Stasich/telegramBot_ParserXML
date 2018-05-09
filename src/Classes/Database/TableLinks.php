<?php
/**
 * Created by PhpStorm.
 * User: stasich
 * Date: 09.05.18
 * Time: 2:53
 */

namespace Classes\Database;

use Classes\Database\DbConnection;
use PDO;

class TableLinks {
    private static $instance = NULL;

    private $pdo;

    private function __construct()
    {
        $this->pdo = DbConnection::getConnection();
        $this->pdo->exec('CREATE TABLE IF NOT EXISTS links (id INTEGER PRIMARY KEY AUTOINCREMENT, link TEXT)');
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
     * @return array
     */
    public function getLinks() {
        $query = 'SELECT link, id FROM links ORDER BY id DESC LIMIT 50';
        $stmt = $this->pdo->query($query);
        $links_arr = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        return $links_arr;
    }

    /**
     * @param array $viewedLinks
     */
    public function addViewedLinksToDb($viewedLinks) {
        if (!empty($viewedLinks)) {
            $query = 'INSERT INTO links (link) values (:link)';
            $stmt = $this->pdo->prepare($query);

            foreach ($viewedLinks as $link) {
                $stmt->execute([
                    'link' => $link,
                ]);
            }
        }
    }
}
