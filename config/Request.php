<?php

namespace CrudGabit\Config;

class Request
{
    /**
     * Obtener valor de POST o GET
     * @param string $key
     * @return string|null
     */
    public static function get(string $key): ?string
    {
        return $_POST[$key] ?? $_GET[$key] ?? null;
    }

    /**
     * Obtener todos los datos POST
     * @param string|null $key
     * @return array|string|null
     */
    public static function post(?string $key = null) : array|string|null
    {
        if ($key) {
            return $_POST[$key] ?? null;
        }
        return $_POST;
    }

    /**
     * Redirigir a URL
     * @param string $path
     * @return void
     */
    public static function redirect(string $path): void
    {
        // Asegurar que empiece con /
        if (!str_starts_with($path, "/")) {
            $path = "/" . $path;
        }
        
        header("Location: {$path}");
        exit;
    }
}
