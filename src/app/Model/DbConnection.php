<?php

namespace app\Model;

use PDO;

class DbConnection {
    private $pdo;

    public function __construct() {
        
        $host = $_ENV['MYSQL_HOST'];  // This should match the service name in your docker-compose.yml
        $database = $_ENV['MYSQL_DATABASE'];
        $username = $_ENV['MYSQL_USER'];
        $password = $_ENV['MYSQL_PASSWORD'];

        $dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";

        try {
            $this->pdo = new PDO($dsn, $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }

    }

    public function getPDO() {
        return $this->pdo;
    }

    public function query($sql) {
        return $this->pdo->query($sql);
    }

    // Implement the prepare method
    public function prepare($sql) {
        return $this->pdo->prepare($sql);
    }
}
