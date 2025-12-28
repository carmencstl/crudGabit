    public static function deleteUserById(int $id): bool {
        error_log("DEBUG - Intentando borrar usuario ID: " . $id);
        $pdo = DataBase::connect();
        $stmt = $pdo->prepare("DELETE FROM usuario WHERE idUsuario = :id");
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $result = $stmt->execute();
        error_log("DEBUG - Resultado delete: " . ($result ? "true" : "false"));
        error_log("DEBUG - Filas afectadas: " . $stmt->rowCount());
        return $result;
    }
