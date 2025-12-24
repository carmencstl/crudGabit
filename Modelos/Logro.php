<?php

namespace CrudGabit\Modelos;

use CrudGabit\Config\DataBase;
use CrudGabit\Config\Session;
use PDO;

    class Logro {
        private int $idLogro;

        private string $nombre {
            set {
                $this->nombre = ucfirst($value);
            }
        }

        private string $descripcion {
            set {
                $this->descripcion = ucfirst($value);
            }
        }

        private int $idCamino;

        private string $fechaCreacion {
            set{
                $this->fechaCreacion = date("d-m-Y", strtotime($value));
            }
        }

        /**
         * Constructor vacío (para fetchObject)
         */
        public function __construct() {}

        /**
         * Crear nuevo logro
         * @return Logro
         * @param string $nombre
         * @param string $descripcion
         * @param int $idCamino
         * @return Logro
         */
        public static function create(string $nombre, string $descripcion, int $idCamino): self
        {
            $logro = new self();
            $logro->nombre = $nombre;
            $logro->descripcion = $descripcion;
            $logro->idCamino = $idCamino;
            return $logro;
        }

        /**
         * Insertar logro en la base de datos
         * @return bool
         */
        public function insertarLogroEnBD(): bool
        {
            $db = DataBase::connect();
            $stmt = $db->prepare("INSERT INTO logro (nombre, descripcion, idCamino) 
                VALUES (:nombre, :descripcion, :idCamino)");

            $stmt->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
            $stmt->bindValue(':descripcion', $this->descripcion, PDO::PARAM_STR);
            $stmt->bindValue(':idCamino', $this->idCamino, PDO::PARAM_INT);

            return $stmt->execute();
        }

        /**
         * Obtener todos los logros del usuario actual
         * @return array<Logro>
         *
         */
        public static function getLogrosByUser(): array
        {
            $db = DataBase::connect();
            $autor = Session::get("id");

            $sql = "SELECT l.* 
                FROM logro l
                INNER JOIN camino c ON l.idCamino = c.idCamino
                WHERE c.autor = :autor";

            $stmt = $db->prepare($sql);
            $stmt->bindValue(":autor", $autor, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_CLASS, Logro::class);
        }

        /**
         * Obtener logro por su ID
         * @return array|null
         * @param int $idLogro
         */
        public static function getById(int $idLogro): ?array
        {
            $db = DataBase::connect();
            $sql = "SELECT l.*, c.nombre as nombreHabito 
                FROM logro l
                INNER JOIN camino c ON l.idCamino = c.idCamino
                WHERE l.idLogro = :idLogro";

            $stmt = $db->prepare($sql);
            $stmt->bindValue(":idLogro", $idLogro, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        }

        /**
         * Eliminar logro por su ID
         * @param int $idLogro
         * @return void
         */
        public static function deleteLogroById(int $idLogro): void
        {
            $db = DataBase::connect();
            $sql = "DELETE FROM logro WHERE idLogro = :idLogro";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(":idLogro", $idLogro, PDO::PARAM_INT);
            $stmt->execute();
        }

        /**
         * Actualizar logro en la base de datos
         * @return bool
         * @param int $idLogro
         * @param string $nombre
         * @param string $descripcion
         */
        public static function actualizarLogro(int $idLogro, string $nombre, string $descripcion): bool
        {
            $db = DataBase::connect();
            $sql = "UPDATE logro 
                    SET nombre = :nombre, descripcion = :descripcion 
                    WHERE idLogro = :idLogro";

            $stmt = $db->prepare($sql);

            $stmt->bindValue(":nombre", $nombre, PDO::PARAM_STR);
            $stmt->bindValue(":descripcion", $descripcion, PDO::PARAM_STR);
            $stmt->bindValue(":idLogro", $idLogro, PDO::PARAM_INT);

            return $stmt->execute();
        }

        /**
         * Buscar logros por nombre o descripción
         * @return array<Logro>
         * @param string $query
         * @param int $autor
         * @return array<Logro>
         */
        public static function search(string $query, int $autor): array
        {
            $db = DataBase::connect();
            $searchTerm = "%{$query}%";

            $sql = "SELECT l.* 
                    FROM logro l
                    INNER JOIN camino c ON l.idCamino = c.idCamino
                    WHERE c.autor = :autor 
                    AND (l.nombre LIKE :search OR l.descripcion LIKE :search)";

            $stmt = $db->prepare($sql);
            $stmt->bindValue(":autor", $autor, PDO::PARAM_INT);
            $stmt->bindValue(":search", $searchTerm, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_CLASS, Logro::class);
        }


        /**
         * Obtener nombre del hábito asociado
         * @return string
         */
        public function getNombreHabito(): string
        {
            $habito = Habito::getById($this->idCamino);
            return $habito ? $habito->getNombre() : "Desconocido";
        }

        // GETTERS
        public function getNombre(): string
        {
            return $this->nombre;
        }

        public function getDescripcion(): string
        {
            return $this->descripcion;
        }

        public function getIdCamino(): int
        {
            return $this->idCamino;
        }

        public function getIdLogro(): int
        {
            return $this->idLogro;
        }

        public function getFechaCreacion(): ?string
        {
            return $this->fechaCreacion;
        }


    }