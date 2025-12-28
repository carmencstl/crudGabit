<?php

namespace CrudGabit\Controladores;

use CrudGabit\Config\Request;
use CrudGabit\Config\Router;
use CrudGabit\Config\Session;
use CrudGabit\Modelos\Usuario;
use CrudGabit\Config\Auth;

class UserController extends BaseController
{

    public function index(): void
    {
        Router::protectAdmin("/dashboard");
        $search = Request::get("search");

        if ($search) {
            $usuarios = Usuario::search($search);
        } else {
            $usuarios = Usuario::getAllUsers();
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
        Router::protectAdmin("/dashboard");

        $idUsuario = Request::post("idUsuario");
        
        if ($idUsuario) {
            Usuario::deleteUserById((int)$idUsuario);
            Session::set("success", "Usuario borrado correctamente");
        } else {
            Session::set("error", "ID de usuario no recibido");
        }
        Request::redirect("/users");
    }

    public function showEdit(): void
    {
        Router::protectAdmin("/dashboard");

        $idUsuario = Request::post("idUsuario") ?? Request::get("idUsuario") ?? Session::get("idUsuarioEdit");


        if (Request::post("idUsuario") || Request::get("idUsuario")) {
            Session::set("idUsuarioEdit", $idUsuario);
        }

        if (!$idUsuario) {

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
            Usuario::actualizarUsuario($idUsuario, $nombreUsuario, $nombre, $apellidos, $email, $password, $rol);
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
