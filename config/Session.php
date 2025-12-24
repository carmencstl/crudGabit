<?php

namespace CrudGabit\Config;
class Session
{
    private const TIEMPO_EXPIRACION = 3600;

    /**
     * Iniciar sesión PHP
     * @return void
     */
    private static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        self::verificarExpiracion();
    }

    /**
     * Inicializar sesión de usuario (guarda ID)
     * @param object $usuario
     */
    public static function init($usuario): void
    {
        self::start();

        // Regenerar ID de sesión por seguridad
        session_regenerate_id(true);

        $_SESSION["id"] = $usuario->getId();
        $_SESSION["tiempo_inicio"] = time();
        $_SESSION["ultima_actividad"] = time();
    }

    /**
     * Verificar si hay sesión activa
     * @return bool
     */
    public static function active(): bool
    {
        self::start();
        return isset($_SESSION["id"]);
    }

    /**
     * Cerrar sesión
     * @return void
     */
    public static function logout(): void
    {
        self::start();
        $_SESSION = [] ;
        session_destroy() ;
    }

    /**
     * Obtener valor de sesión
     * @param string $key
     * @param mixed $default
     * @return mixed
     *
     */
    public static function get(string $key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Establecer valor en sesión
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set(string $key, $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }


    /**
     * Eliminar una clave
     * @param string $key
     * @return void
     */
    public static function delete(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    /**
     * Verificar y gestionar expiración de sesión
     * @return void
     *
     */
    private static function verificarExpiracion(): void
    {
        if (!isset($_SESSION["ultima_actividad"])) {
            return;
        }

        $tiempoInactivo = time() - $_SESSION["ultima_actividad"];

        if ($tiempoInactivo > self::TIEMPO_EXPIRACION) {
            $_SESSION = [];
            session_destroy();
            return;
        }

        $_SESSION["ultima_actividad"] = time();
    }
}
