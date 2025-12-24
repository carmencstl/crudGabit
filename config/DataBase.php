<?php
namespace CrudGabit\Config;

use PDO;
use PDOException;

    final class DataBase {
        private const DBHOST = "dwes-db";
        private const DBUSER = "root";
        private const DBPASS = "root";
        private const DBNAME = "crudGabit";
        private static ?DataBase $instance = null;
        private ?PDO $pdo = null;

        /**
         * Constructor privado para evitar instanciación externa
         */
        private function __construct()
        {
            try {
                $dsn = "mysql:host=" . self::DBHOST . ";dbname=" . self::DBNAME . ";charset=utf8";
                $this->pdo = PDO\Mysql::connect($dsn, self::DBUSER, self::DBPASS); #De clase PDO\MySQL

            } catch (PDOException $pdoe) {
                die("ERROR {$pdoe->getMessage()}");
            }
            return $this->pdo;
        }

        /**
         * Obtiene la instancia única de la clase DataBase
         * @return DataBase
         */
        public static function getInstance(): DataBase
        {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * Obtiene la conexión PDO
         * @return PDO
         */
        public function getConnection(): PDO
        {
            return $this->pdo;
        }

        /**
         * Método estático para obtener la conexión PDO directamente
         * @return PDO
         */
        public static function connect(): PDO
        {
            return self::getInstance()->getConnection();
        }
    }