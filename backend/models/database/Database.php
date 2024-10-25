<?php

namespace Backend\Models\Database;

use PDO;
use PDOException;

class Database {
    public function getConnection(): ?PDO {
        $host = getenv('DB_HOST');
        $dbname = getenv('DB_NAME');
        $user = getenv('DB_USER');
        $password = getenv('DB_PASS');

        try {
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
            $conn = new PDO($dsn, $user, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            error_log("Connection error: " . $exception->getMessage());
            return null;
        }

        return $conn;
    }
}