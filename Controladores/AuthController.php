<?php

namespace CrudGabit\Controladores;

use CrudGabit\Config\Auth;
use CrudGabit\Config\Session;
use CrudGabit\Config\Request;
use CrudGabit\Modelos\Usuario;

    class AuthController extends BaseController
    {

        /**
         * Mostrar formulario de login
         * @return void
         */
        public function showLogin(): void
        {
            $error = Session::get("error");
            Session::delete("error");

            if (Session::active()) {
                Request::redirect("/dashboard");
            }
            echo $this->render("auth/login.twig", [
                "error" => $error
                ]);
        }


        /**
         * Procesar login
         * @return void
         */
        public function login(): void
        {
            $email = Request::get("email");
            $password = Request::get("password");

            if (Auth::login($email, $password)) {
                Request::redirect("/crudGabit/dashboard");
            }
            else{
                Session::set("error", "Credenciales inválidas. Inténtalo de nuevo.");
                Request::redirect("/crudGabit/login");
            }
        }

        /**
         * Mostrar formulario de registro
         * @return void
         */
        public function showRegister(): void
        {
            $error = Session::get("error");
            Session::delete("error");

            if(Session::active()) {
                Request::redirect("/dashboard");
            }
            echo $this->render("auth/register.twig", [
                "error" => $error
            ]);
        }


        public function register(): void
        {
            $email = Request::get("email");
            $nombreUsuario = Request::get("nombreUsuario");
            $resultado = null;

            if ((is_object(Usuario::getByEmail($email))) || (is_object(Usuario::getByNombreUsuario($nombreUsuario)))){
                Session::set("error", "Los datos ingresados ya están en uso. Por favor, elige otros.");
            }
            else {
                $usuario = Usuario::create(
                    Request::get("nombreUsuario"),
                    Request::get("nombre"),
                    Request::get("apellidos"),
                    $email,
                    Request::get("password"),
                    "usuario"
                );
                if ($usuario->insertarUsuario()) {
                    Auth::login($email, Request::get("password"));
                    Request::redirect("/dashboard");
                } else {
                    Session::set("error", "Error al registrar el usuario. Inténtalo de nuevo.");
                }
            }
            Request::redirect("/crudGabit/register");
        }

        /**
         * Cerrar sesión
         * @return void
         */
        public function logout(): void
        {
            Auth::logout();
            Request::redirect("/crudGabit/login");
        }
    }