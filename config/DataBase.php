<?php
namespace CrudGabit\Config;

use PDO;
use PDOException;

final class DataBase {
    private static ?DataBase $instance = null;
    private ?PDO $pdo = null;

    private function __construct()
    {
        // Railway pone las variables en $_ENV y $_SERVER
        $dbHost = $_ENV["DB_HOST"] ?? $_SERVER["DB_HOST"] ?? "localhost";
        $dbName = $_ENV["DB_NAME"] ?? $_SERVER["DB_NAME"] ?? "crudGabit";
        $dbUser = $_ENV["DB_USER"] ?? $_SERVER["DB_USER"] ?? "root";
        $dbPass = $_ENV["DB_PASS"] ?? $_SERVER["DB_PASS"] ?? "root";

        try {
            $dsn = "mysql:host=" . $dbHost . ";dbname=" . $dbName . ";charset=utf8";

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $this->pdo = new PDO($dsn, $dbUser, $dbPass, $options);

        } catch (PDOException $pdoe) {
            die("ERROR DE CONEXIÓN: {$pdoe->getMessage()}");
        }
    }

    public static function getInstance(): DataBase
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    public static function connect(): PDO
    {
        return self::getInstance()->getConnection();
    }
}
