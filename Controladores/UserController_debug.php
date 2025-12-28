    public function borrarUsuario(): void
    {
        Router::protectAdmin("/dashboard");

        echo "<h1>DEBUG - Método borrarUsuario ejecutado</h1>";
        echo "<pre>";
        echo "POST recibido:\n";
        print_r($_POST);
        echo "\nRequest::post('idUsuario'): " . Request::post("idUsuario");
        echo "</pre>";
        die(); // Detener aquí para ver si llega
        
        $idUsuario = Request::post("idUsuario");
        
        if ($idUsuario) {
            Usuario::deleteUserById((int)$idUsuario);
            Session::set("success", "Usuario borrado correctamente");
        } else {
            Session::set("error", "ID de usuario no recibido");
        }

        Request::redirect("/users");
    }
