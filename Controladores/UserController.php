<?php

namespace CrudGabit\Controladores;

use CrudGabit\Config\Request;
use CrudGabit\Config\Router;
use CrudGabit\Config\Session;
use CrudGabit\Modelos\Usuario;
use CrudGabit\Config\Auth;

class UserController extends BaseController
{
    private function log($message) {
        file_put_contents('/tmp/user_controller.log', date('Y-m-d H:i:s') . " - " . $message . "\n", FILE_APPEND);
    }

    public function index(): void
    {
        $this->log("INDEX EJECUTADO");
        Router::protectAdmin("/dashboard");

        $search = Request::get("search");
        $this->log("Search parameter: " . ($search ?? "NULL"));

        if ($search) {
            $usuarios = Usuario::search($search);
            $this->log("Searching, found: " . count($usuarios));
        } else {
            $usuarios = Usuario::getAllUsers();
            $this->log("Getting all users: " . count($usuarios));
        }

        $success = Session::get("success");
        Session::delete("success");

        echo $this->render("users/index.twig", [
            "usuarios" => $usuarios,
            "success" => $success,
            "search" => $search
        ]);
    }

    public function borrarUsuario(): void
    {
        $this->log("BORRAR USUARIO EJECUTADO");
        Router::protectAdmin("/dashboard");

        $idUsuario = Request::post("idUsuario");
        $this->log("ID recibido: " . ($idUsuario ?? "NULL"));
        $this->log("POST data: " . print_r($_POST, true));
        
        if ($idUsuario) {
            $this->log("Intentando borrar ID: " . $idUsuario);
            Usuario::deleteUserById((int)$idUsuario);
            Session::set("success", "Usuario borrado correctamente");
            $this->log("Usuario borrado, success message set");
        } else {
            Session::set("error", "ID de usuario no recibido");
            $this->log("ERROR: ID no recibido");
        }

        $this->log("Redirigiendo a /users");
        Request::redirect("/users");
    }

    public function showEdit(): void
    {
        $this->log("SHOW EDIT EJECUTADO");
        Router::protectAdmin("/dashboard");

        $idUsuario = Request::post("idUsuario") ?? Request::get("idUsuario") ?? Session::get("idUsuarioEdit");
        $this->log("ID para editar: " . ($idUsuario ?? "NULL"));

        if (Request::post("idUsuario") || Request::get("idUsuario")) {
            Session::set("idUsuarioEdit", $idUsuario);
        }

        if (!$idUsuario) {
            $this->log("No ID, redirigiendo a /users");
            Request::redirect("/users");
        }

        $usuario = Usuario::getById((int)$idUsuario);
        $success = Session::get("success");
        $error = Session::get("error");
        Session::delete("success");
        Session::delete("error");

        echo $this->render("users/edit.twig", [
            "usuario" => $usuario,
            "success" => $success,
            "error" => $error
        ]);
    }

    public function editarUsuario(): void
    {
        $this->log("EDITAR USUARIO EJECUTADO");
        Router::protectAdmin("/dashboard");

        $idUsuario = (int)Request::post("idUsuario");
        $nombreUsuario = Request::post("nombreUsuario");
        $nombre = Request::post("nombre");
        $apellidos = Request::post("apellidos");
        $email = Request::post("email");
        $password = Request::post("password");
        $rol = Request::post("rol");

        if ((Usuario::getByEmail($email) && Usuario::getByEmail($email)->getId() !== $idUsuario) ||
            (Usuario::getByNombreUsuario($nombreUsuario) && Usuario::getByNombreUsuario($nombreUsuario)->getId() !== $idUsuario)) {
            Session::set("error", "El email o nombre de usuario ya están en uso.");
        } else {
            Usuario::updateUsuario($idUsuario, $nombreUsuario, $nombre, $apellidos, $email, $password, $rol);
            Session::set("success", "Usuario actualizado correctamente.");
        }

        Request::redirect("/users/edit");
    }

    public function showCreate(): void
    {
        Router::protectAdmin("/dashboard");

        $error = Session::get("error");
        Session::delete("error");

        echo $this->render("users/create.twig", [
            "error" => $error
        ]);
    }

    public function createUser(): void
    {
        Router::protectAdmin("/dashboard");

        $nombreUsuario = Request::post("nombreUsuario");
        $nombre = Request::post("nombre");
        $apellidos = Request::post("apellidos");
        $email = Request::post("email");
        $password = Request::post("password");
        $rol = Request::post("rol");

        if (Usuario::getByEmail($email) || Usuario::getByNombreUsuario($nombreUsuario)) {
            Session::set("error", "El email o nombre de usuario ya están en uso.");
        } else {
            $usuario = Usuario::create($nombreUsuario, $nombre, $apellidos, $email, $password, $rol);
            if ($usuario->insertarUsuario()) {
                Session::set("success", "Usuario creado correctamente.");
                Request::redirect("/users");
            } else {
                Session::set("error", "Error al crear el usuario.");
            }
        }

        Request::redirect("/users/create");
    }
}
