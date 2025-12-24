<?php

    namespace CrudGabit\Config ;

    use CrudGabit\Config\Session;
    use CrudGabit\Modelos\Usuario ;

    class Auth
    {
        /**
         * Iniciar sesión
         * @param string $email
         * @param string $pass
         * @return boolean
         */
        public static function login(string $email, string $pass): bool
        {
            $usuario = Usuario::getByEmailAndPassword($email, $pass) ;

            if (is_object($usuario)) Session::init($usuario) ;

            return is_object($usuario) ;
        }

        /**
         * Obtener el usuario activo
         * @return Usuario|false
         */
        public static function user(): Usuario|false
        {
            $res = false ;
            if (Session::active()):
                $res =  Usuario::getById(Session::get("id")) ;
            endif ;
            return $res ;
        }

        /**
         * Verificar rol del usuario actual
         * @return string
         */
        public static function checkRol(): bool
        {
            $resultado = false;

            if (Session::active()) {
            $usuario = self::user();
                if ($usuario) {
                    $resultado = Usuario::comprobarRol($usuario->getId());
                }
            }
            return $resultado === "admin";
        }


        /**
         * Cerrar sesión
         * @return void
         */
        public static function logout(): void
        {
            Session::logout() ;
        }


    }