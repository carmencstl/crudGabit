<?php

namespace CrudGabit\Modelos;

use CrudGabit\Config\DataBase;
use CrudGabit\Config\Session;
use PDO;

class Habito {
    private int $idCamino;

    private string $nombre {
        set {
            $this->nombre = ucfirst($value);
        }
    }

    private string $descripcion;
    private int $autor;
    private string $categoria;

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
     * Crear nuevo hábito
     * @return Habito
     * @param string $nombre
     * @param string $descripcion
     * @param int $autor
     * @param string $categoria
     * @return Habito
     */
    public static function create(string $nombre, string $descripcion, int $autor, string $categoria): self
    {
        $habito = new self();
        $habito->nombre = $nombre;
        $habito->descripcion = $descripcion;
        $habito->autor = $autor;
        $habito->categoria = $categoria;
        return $habito;
    }

    /**
     * Eliminar hábito por su ID
     * @param int $idCamino
     * @return void
     */
    public static function deleteHabitById(int $idCamino): void
    {
        $db = DataBase::connect();
        $sql = "DELETE FROM camino WHERE idCamino = :idCamino";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(":idCamino", $idCamino, PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * Obtener todos los hábitos del usuario actual
     * @return array
     */
    public static function getHabitsByUser(): array
    {
        $db = DataBase::connect();
        $autor = Session::get("id");

        $sql = "SELECT c.*  
        FROM camino c 
        WHERE c.autor = :autor";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(":autor", $autor, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, Habito::class);
    }

    /**
     * Obtener hábito por su ID
     * @return Habito|null
     * @param int $idCamino
     */
    public static function getById(int $idCamino): ?Habito
    {
        $db = DataBase::connect();
        $sql = "SELECT * FROM camino WHERE idCamino = :idCamino";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(":idCamino", $idCamino, PDO::PARAM_INT);
        $stmt->execute();
        $habito = $stmt->fetchObject(Habito::class);
        return $habito ?: null;
    }

    /**
     * Actualizar hábito en la base de datos
     * @return bool
     * @param int $idCamino
     * @param string $nombre
     * @param string $descripcion
     * @param string $categoria
     */
    public static function actualizarHabito(int $idCamino, string $nombre, string $descripcion, string $categoria): bool
    {
        $db = DataBase::connect();
        $sql = "UPDATE camino 
                SET nombre = :nombre, descripcion = :descripcion, categoria = :categoria 
                WHERE idCamino = :idCamino";
        $stmt = $db->prepare($sql);

        $stmt->bindValue(":nombre", $nombre, PDO::PARAM_STR);
        $stmt->bindParam(":descripcion", $descripcion, PDO::PARAM_STR);
        $stmt->bindParam(":categoria", $categoria, PDO::PARAM_STR);
        $stmt->bindParam(":idCamino", $idCamino, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Insertar hábito en la base de datos
     * @return bool
     */
    public function insertarHabitoEnBD(): bool
    {
        $db = DataBase::connect();
        $stmt = $db->prepare("INSERT INTO camino (nombre, descripcion, autor, categoria) 
            VALUES (:nombre, :descripcion, :autor, :categoria)");

        $stmt->bindValue(":nombre", $this->nombre, PDO::PARAM_STR);
        $stmt->bindParam(":descripcion", $this->descripcion, PDO::PARAM_STR);
        $stmt->bindParam(":autor", $this->autor, PDO::PARAM_INT);
        $stmt->bindParam(":categoria", $this->categoria, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Buscar hábitos por nombre, descripción o categoría
     * @return array
     * @param string $query
     * @param int $autor
     * @return array
     */
    public static function search(string $query, int $autor): array
    {
        $db = DataBase::connect();
        $searchTerm = "%{$query}%";

        $sql = "SELECT c.* 
        FROM camino c 
        WHERE c.autor = :autor 
        AND (c.nombre LIKE :search 
             OR c.descripcion LIKE :search 
             OR c.categoria LIKE :search)";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(":autor", $autor, PDO::PARAM_INT);
        $stmt->bindValue(":search", $searchTerm, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_CLASS, Habito::class);
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    public function getCategoria(): string
    {
        return $this->categoria;
    }

    public function getIdCamino(): int
    {
        return $this->idCamino;
    }

    public function getFechaCreacion(): string
    {
        return $this->fechaCreacion;
    }

    public function getNombreAutor(): string
    {
        $usuario = Usuario::getById($this->autor);
        return $usuario ? $usuario->getNombreUsuario() : "Desconocido";
    }
}