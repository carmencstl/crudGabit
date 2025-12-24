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
         * @return array
         */
        public static function post(?string $key = null) : array|string|null
        {
            if ($key) {
                return $_POST[$key] ?? null;
            }

            return $_POST;
        }

        /**
         * Redirigir a URL con base /crudGabit
         * @param string $url
         * @return never
         */
        public static function redirect(string $path): void
        {
            if (!str_contains($path, "/crudGabit")) {
                $path = "/crudGabit" . (str_starts_with($path, "/") ? $path : "/" . $path);
            }
            header("Location: {$path}");
            exit;
        }

    }