<?php

namespace CrudGabit\Modelos;

use CrudGabit\Config\DataBase;
use PDO;

class Usuario {
    private int $idUsuario;

    private string $nombreUsuario {
        set {
            $this->nombreUsuario = strtolower($value);
        }
    }

    private string $nombre {
        set {
            $this->nombre = ucfirst($value);
        }
    }

    private string $apellidos {
        set {
            $this->apellidos = ucwords($value);
        }
    }

    private string $email;
    private string $password;
    private string $rol;

    private string $fechaCreacion{
        set{
            $this->fechaCreacion = date("d-m-Y", strtotime($value));
        }
    }

    /**
     * Constructor vacío (para fetchObject)
     */
    public function __construct() {}

    /**
     * Crear nueva instancia de Usuario
     * @return Usuario
     * @param string $nombreUsuario
     * @param string $nombre
     * @param string $apellidos
     * @param string|null $email
     * @param string $password
     * @param string $rol
     * @return Usuario
     *
     */
    public static function create(
        string $nombreUsuario,
        string $nombre,
        string $apellidos,
        ?string $email,
        string $password,
        string $rol = "usuario"
    ): Usuario {
        $usuario = new self();
        $usuario->nombreUsuario = $nombreUsuario;
        $usuario->nombre = $nombre;
        $usuario->apellidos = $apellidos;
        $usuario->email = $email;
        $usuario->password = $password;
        $usuario->rol = $rol;
        return $usuario;
    }

    /**
     * Insertar el usuario en la base de datos
     * @return bool
     */
    public function insertarUsuario(): bool {
        $pdo = DataBase::connect();
        $stmt = $pdo->prepare("INSERT INTO usuario (nombreUsuario, nombre, apellidos, email, password, rol) 
                               VALUES (:nu, :n, :a, :e, :p, :r)");

        $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);

        $stmt->bindValue(":nu", $this->nombreUsuario, PDO::PARAM_STR);
        $stmt->bindValue(":n", $this->nombre, PDO::PARAM_STR);
        $stmt->bindValue(":a", $this->apellidos, PDO::PARAM_STR);
        $stmt->bindValue(":e", $this->email, PDO::PARAM_STR);
        $stmt->bindValue(":p", $hashedPassword, PDO::PARAM_STR);
        $stmt->bindValue(":r", $this->rol, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Actualizar usuario existente
     * @return bool
     * @param int $idUsuario
     * @param string $nombreUsuario
     * @param string $nombre
     * @param string $apellidos
     * @param string $email
     * @param string $rol
     * @return bool
     */
    public static function actualizarUsuario(
        int $idUsuario,
        string $nombreUsuario,
        string $nombre,
        string $apellidos,
        string $email,
        string $rol
    ): bool {
        $pdo = DataBase::connect();
        $stmt = $pdo->prepare("UPDATE usuario 
                               SET nombreUsuario = :nombreUsuario,
                                   nombre = :nombre,
                                   apellidos = :apellidos,
                                   email = :email,
                                   rol = :rol
                               WHERE idUsuario = :idUsuario");

        $nombreUsuarioLower = strtolower($nombreUsuario);
        $nombreCapital = ucfirst($nombre);
        $apellidosCapital = ucwords($apellidos);

        $stmt->bindValue(":nombreUsuario", $nombreUsuarioLower, PDO::PARAM_STR);
        $stmt->bindValue(":nombre", $nombreCapital, PDO::PARAM_STR);
        $stmt->bindValue(":apellidos", $apellidosCapital, PDO::PARAM_STR);
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->bindValue(":rol", $rol, PDO::PARAM_STR);
        $stmt->bindValue(":idUsuario", $idUsuario, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Traer todos los usuarios
     * @return Usuario[]
     */
    public static function getAllUsers(): array {
        $pdo = DataBase::connect();
        return $pdo->query("SELECT * FROM usuario")->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    /**
     * Obtener usuario por ID
     * @return Usuario|null
     * @param int $id
     */
    public static function getById(int $id): ?Usuario {
        $pdo = DataBase::connect();
        $stmt = $pdo->prepare("SELECT * FROM usuario WHERE idUsuario = :id");
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $usuario = $stmt->fetchObject(self::class);
        return $usuario ?: null;
    }

    /**
     * Eliminar usuario por su ID
     * @return bool
     * @param int $id
     * @return bool
     */
    public static function deleteUserById(int $id): bool {
        $pdo = DataBase::connect();
        $stmt = $pdo->prepare("DELETE FROM usuario WHERE idUsuario = :id");
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Buscar usuario por email
     * @return Usuario|null
     * @param string $email
     * @return Usuario|null
     */
    public static function getByEmail(string $email): ?Usuario {
        $pdo = DataBase::connect();
        $stmt = $pdo->prepare("SELECT * FROM usuario WHERE email = :email");
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->execute();
        $usuario = $stmt->fetchObject(self::class);
        return $usuario ?: null;
    }

    /**
     * Buscar usuario por nombreUsuario
     * @return Usuario|null
     * @param string $nombreUsuario
     * @return Usuario|null
     */
    public static function getByNombreUsuario(string $nombreUsuario): ?Usuario {
        $pdo = DataBase::connect();
        $nombreUsuarioLower = strtolower($nombreUsuario);
        $stmt = $pdo->prepare("SELECT * FROM usuario WHERE nombreUsuario = :nombreUsuario");
        $stmt->bindValue(":nombreUsuario", $nombreUsuarioLower, PDO::PARAM_STR);
        $stmt->execute();
        $usuario = $stmt->fetchObject(self::class);
        return $usuario ?: null;
    }

    /**
     * Buscar usuario por email y password
     * @return Usuario|null
     * @param string $email
     * @param string $password
     * @return Usuario|null
     */
    public static function getByEmailAndPassword(string $email, string $password): ?Usuario {
        $pdo = DataBase::connect();
        $resultado = null;

        $stmt = $pdo->prepare("SELECT * FROM usuario WHERE email = :email");
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->execute();
        $usuario = $stmt->fetchObject(self::class);

        if ($usuario && password_verify($password, $usuario->password)) {
            $resultado = $usuario;
        }

        return $resultado;
    }

    /**
     * Comprobar el rol de un usuario por su ID
     * @return string
     * @param int $idUsuario
     * @return string
     */
    public static function comprobarRol(int $idUsuario): string {
        $pdo = DataBase::connect();
        $stmt = $pdo->prepare("SELECT rol FROM usuario WHERE idUsuario = :id");
        $stmt->bindValue(":id", $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        $rol = $stmt->fetchColumn();
        return $rol ?: "usuario";
    }

    /**
     * Buscar usuarios por nombre, apellidos, email o username
     * @return Usuario[]
     * @param string $query
     * @return Usuario[]
     */
    public static function search(string $query): array {
        $pdo = DataBase::connect();
        $searchTerm = "%{$query}%";

        $stmt = $pdo->prepare("SELECT * FROM usuario 
                          WHERE nombreUsuario LIKE :search 
                          OR nombre LIKE :search 
                          OR apellidos LIKE :search 
                          OR email LIKE :search");

        $stmt->bindValue(":search", $searchTerm, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_CLASS, Usuario::class);
    }

    // GETTERS
    public function getId(): int { return $this->idUsuario; }
    public function getNombreUsuario(): string { return $this->nombreUsuario; }
    public function getNombre(): string { return $this->nombre; }
    public function getApellidos(): string { return $this->apellidos; }
    public function getEmail(): ?string { return $this->email; }
    public function getRol(): string { return $this->rol; }

    public function getFechaCreacion(): ?string
    {
        return $this->fechaCreacion;
    }


}