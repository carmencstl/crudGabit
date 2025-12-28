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

    /**
     * Registrar ruta GET
     * @param string $ruta
     * @param array $controlador
     * @return void
     */
    public function get(string $ruta, array $controlador): void
    {
        $this->rutasGET[$ruta] = $controlador;
    }

    /**
     * Registrar ruta POST
     * @param string $ruta
     * @param array $controlador
     * @return void
     */
    public function post(string $ruta, array $controlador): void
    {
        $this->rutasPOST[$ruta] = $controlador;
    }

    /**
     * Preparar URL para comparación
     * @param string $url
     * @return string
     */
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

    /**
     * Ejecutar el router
     * @return void
     */
    public function run(): void
    {
        $metodo = $_SERVER["REQUEST_METHOD"];
        $urlOriginal = $_SERVER["REQUEST_URI"];
        $url = $this->prepararUrl($urlOriginal);

        if ($metodo === "GET") {
            $this->ejecutarRuta($url, $this->rutasGET);
        } elseif ($metodo === "POST") {
            $this->ejecutarRuta($url, $this->rutasPOST);
        } else {
            $this->error404();
        }
    }

    /**
     * Ejecutar la ruta correspondiente
     * @param string $url
     * @param array $rutas
     * @return void
     */
    private function ejecutarRuta(string $url, array $rutas): void
    {
        if (isset($rutas[$url])) {
            $this->llamarControlador($rutas[$url]);
        } else {
            $this->error404();
        }
    }

    /**
     * Llamar al controlador correspondiente
     * @param array $controlador
     * @return void
     */
    private function llamarControlador(array $controlador): void
    {
        [$clase, $metodo] = $controlador;

        if (is_string($clase)) {
            $clase = new $clase();
        }

        $clase->$metodo();
    }

    /**
     * Mostrar página de error 404
     * @return void
     */
    private function error404(): void
    {
        http_response_code(404);
        echo "<!DOCTYPE html>
        <html>
        <head>
            <title>404</title>
        </head>
        <body>
            <h1>404 - Página no encontrada</h1>
            <a href=\"/dashboard\">Volver al Dashboard</a>
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
