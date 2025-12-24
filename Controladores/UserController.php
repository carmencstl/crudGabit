<?php

namespace CrudGabit\Controladores;

use CrudGabit\Config\Request;
use CrudGabit\Config\Router;
use CrudGabit\Config\Session;
use CrudGabit\Modelos\Usuario;
use CrudGabit\Config\Auth;

class UserController extends BaseController
{

    /**
     * Mostrar la lista de usuarios
     * @return void
     *
     */
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

    /**
     * Borrar un usuario por su ID
     * @return void
     */
    public function borrarUsuario(): void
    {
        Router::protectAdmin("/dashboard");

        $idUsuario = Request::post("idUsuario");
        Usuario::deleteUserById((int)$idUsuario);

        Session::set("success", "Usuario borrado correctamente");

        Request::redirect("/crudGabit/users");
    }


    /**
     * Mostrar el formulario de edición de usuario
     * @return void
     */
    public function showEdit(): void
    {
        Router::protectAdmin("/dashboard");

        $idUsuario = Request::post("idUsuario") ?? Request::get("idUsuario") ?? Session::get("idUsuarioEdit");

        if (Request::post("idUsuario") || Request::get("idUsuario")) {
            Session::set("idUsuarioEdit", $idUsuario);
        }

        if (!$idUsuario) {
            Request::redirect("/crudGabit/users");
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

    /**
     * Editar un usuario existente
     * @return void
     */
    public function editarUsuario(): void
    {
        Router::protectAdmin("/dashboard");

        $idUsuario = (int)Request::post("idUsuario");
        $email = Request::post("email");
        $nombreUsuario = Request::post("nombreUsuario");

        $usuarioExistente = Usuario::getByEmail($email);
        $nombreUsuarioExistente = Usuario::getByNombreUsuario($nombreUsuario);

        if ($usuarioExistente && $usuarioExistente->getId() != $idUsuario) {
            Session::set("error", "El email ya está registrado por otro usuario.");
        } elseif ($nombreUsuarioExistente && $nombreUsuarioExistente->getId() != $idUsuario) {
            Session::set("error", "El nombre de usuario ya está en uso por otro usuario.");
        } else {
            $nombre = Request::post("nombre");
            $rol = Request::post("rol");
            $apellidos = Request::post("apellidos");

            Usuario::actualizarUsuario($idUsuario, $nombreUsuario, $nombre, $apellidos, $email, $rol);
            Session::set("success", "Usuario actualizado correctamente.");
        }

        Session::set("idUsuarioEdit", $idUsuario);
        Request::redirect("/crudGabit/users/edit");
    }

    /**
     * Mostrar el formulario de creación de usuario
     * @return void
     */
    public function showCreate(): void
    {
        Router::protectAdmin("/dashboard");

        $success = Session::get("success");
        $error = Session::get("error");

        Session::delete("success");
        Session::delete("error");

        echo $this->render("users/create.twig", [
            "success" => $success,
            "error" => $error
        ]);
    }

    /**
     * Crear un nuevo usuario
     * @return void
     */
    public static function createUser(): void
    {
        $email = Request::post("email");
        $nombreUsuario = Request::post("nombreUsuario");

        if (is_object(Usuario::getByEmail($email))) {
            Session::set("error", "El email ya está registrado. Por favor, usa otro email.");
        } elseif (is_object(Usuario::getByNombreUsuario($nombreUsuario))) {
            Session::set("error", "El nombre de usuario ya está en uso. Por favor, elige otro.");
        } else {
            $usuario = Usuario::create(
                Request::post("nombreUsuario") ?? "",
                Request::post("nombre") ?? "",
                Request::post("apellidos") ?? "",
                Request::post("email") ?? "",
                Request::post("password") ?? "",
                Request::post("rol") ?? "usuario"
            );

            if ($usuario->insertarUsuario()) {
                Session::set("success", "Usuario creado correctamente.");
            } else {
                Session::set("error", "Error al crear el usuario. Inténtalo de nuevo.");
            }
        }
        Request::redirect("/crudGabit/users/create");
    }


}