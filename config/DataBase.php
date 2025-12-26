<?php
namespace CrudGabit\Config;

use PDO;
use PDOException;

final class DataBase {
    private static ?DataBase $instance = null;
    private ?PDO $pdo = null;

    /**
     * Constructor privado para evitar instanciación externa
     */
    private function __construct()
    {
        // Obtener variables de entorno con valores por defecto
        $dbHost = getenv("DB_HOST") ?: "localhost";
        $dbName = getenv("DB_NAME") ?: "crudGabit";
        $dbUser = getenv("DB_USER") ?: "root";
        $dbPass = getenv("DB_PASS") ?: "root";

        try {
            $dsn = "mysql:host=" . $dbHost . ";dbname=" . $dbName . ";charset=utf8";

            // Opciones de PDO
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