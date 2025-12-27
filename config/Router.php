<?php

namespace CrudGabit\Config;

use CrudGabit\Config\Request;

class Router
{
    private array $rutasGET = [];
    private array $rutasPOST = [];
    private string $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, "/");
    }

    public function get(string $ruta, array $controlador): void
    {
        $this->rutasGET[$ruta] = $controlador;
    }

    public function post(string $ruta, array $controlador): void
    {
        $this->rutasPOST[$ruta] = $controlador;
    }

    private function prepararUrl(string $url): string
    {
        $url = strtok($url, "?");

        if (str_starts_with($url, $this->basePath)) {
            $url = substr($url, strlen($this->basePath));
        }

        if (empty($url) || $url[0] !== "/") {
            $url = "/" . $url;
        }

        if ($url !== "/" && str_ends_with($url, "/")) {
            $url = rtrim($url, "/");
        }

        return $url;
    }

    public function run(): void
    {
        $metodo = $_SERVER["REQUEST_METHOD"];
        $urlOriginal = $_SERVER["REQUEST_URI"];
        $url = $this->prepararUrl($urlOriginal);

        // DEBUG
        error_log("Router DEBUG - Método: $metodo");
        error_log("Router DEBUG - URL original: $urlOriginal");
        error_log("Router DEBUG - URL preparada: $url");
        error_log("Router DEBUG - Rutas GET registradas: " . implode(", ", array_keys($this->rutasGET)));

        if ($metodo === "GET") {
            $this->ejecutarRuta($url, $this->rutasGET);
        } elseif ($metodo === "POST") {
            $this->ejecutarRuta($url, $this->rutasPOST);
        } else {
            $this->error404();
        }
    }

    private function ejecutarRuta(string $url, array $rutas): void
    {
        if (isset($rutas[$url])) {
            error_log("Router DEBUG - Ruta encontrada: $url");
            $this->llamarControlador($rutas[$url]);
        } else {
            error_log("Router DEBUG - Ruta NO encontrada: $url");
            $this->error404();
        }
    }

    private function llamarControlador(array $controlador): void
    {
        [$clase, $metodo] = $controlador;

        if (is_string($clase)) {
            $clase = new $clase();
        }

        $clase->$metodo();
    }

    private function error404(): void
    {
        http_response_code(404);
        echo "<!DOCTYPE html>
<html>
<head>
    <title>404</title>
    <style>
        body { font-family: Arial; text-align: center; padding: 50px; }
        h1 { color: #05576B; }
    </style>
</head>
<body>
    <h1>404 - Página no encontrada</h1>
    <a href='/dashboard'>Volver al Dashboard</a>
</body>
</html>";
        exit();
    }

    public static function protectAdmin($url): void
    {
        if (!Auth::checkRol()) {
            Request::redirect($url);
        }
    }
}
