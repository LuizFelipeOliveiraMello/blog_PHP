<?php
class Database {
    private $db;

    public function __construct($dbPath) {
        try {
            $this->db = new SQLite3($dbPath);
        } catch (Exception $e) {
            die('Unable to connect to the database: ' . $e->getMessage());
        }
    }

    public function query($sql, $params = array()) {
        try {
            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            $stmt->execute();
        } catch (Exception $e) {
            die('Error executing query: ' . $e->getMessage());
        }
    }

    public function select($sql, $params = array()) {
        try {
            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            return $stmt->execute();
        } catch (Exception $e) {
            die('Error executing select query: ' . $e->getMessage());
        }
    }

    public function beginTransaction() {
        return $this->db->exec('BEGIN TRANSACTION;');
    }

    public function commit() {
        return $this->db->exec('COMMIT;');
    }

    public function rollback() {
        return $this->db->exec('ROLLBACK;');
    }

    public function lastInsertRowID() {
        return $this->db->lastInsertRowid();
    }

    public function close() {
        $this->db->close();
    }
}

?>
