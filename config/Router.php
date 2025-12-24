<?php

namespace CrudGabit\Config;

use CrudGabit\Config\Request;
use CrudGabit\Config\Session;
use CrudGabit\Config\Auth;

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
     * Registrar una ruta GET
     * @param string $ruta
     * @param array $controlador
     * @return void
     */
    public function get(string $ruta, array $controlador): void
    {
        $this->rutasGET[$ruta] = $controlador;
    }

    /**
     * Registrar una ruta POST
     * @param string $ruta
     * @param array $controlador
     * @return void
     */
    public function post(string $ruta, array $controlador): void
    {
        $this->rutasPOST[$ruta] = $controlador;
    }

    /**
     * Limpiar y normalizar la URL
     * @param string $url
     * @return string
     */
    private function prepararUrl(string $url): string
    {
        // Quitar parámetros GET
        $url = strtok($url, "?");

        // Quitar basePath (/crudGabit)
        if (str_starts_with($url, $this->basePath)) {
            $url = substr($url, strlen($this->basePath));
        }

        // Asegurar que empieza con /
        if (empty($url) || $url[0] !== "/") {
            $url = "/" . $url;
        }

        // Quitar / del final (excepto raíz)
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
        $url = $this->prepararUrl($_SERVER["REQUEST_URI"]);

        if ($metodo === "GET") {
            $this->ejecutarRuta($url, $this->rutasGET);
        } elseif ($metodo === "POST") {
            $this->ejecutarRuta($url, $this->rutasPOST);
        } else {
            $this->error404();
        }
    }

    /**
     * Buscar y ejecutar la ruta correspondiente
     * @param string $url
     * @param array $rutas
     * @return void
     */
    private function ejecutarRuta(string $url, array $rutas): void
    {
        if (isset($rutas[$url])) {
            $this->llamarControlador($rutas[$url]);
        }
        else{
            $this->error404();
        }
    }

    /**
     * Llamar al método del controlador
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
     * Mostrar error 404
     * @return void
     */
    private function error404(): void
    {
        http_response_code(404);
        Request::redirect("/dashboard");
    }

    /**
     * Proteger ruta para administradores
     * @param string $url
     * @return void
     */
    public static function protectAdmin($url): void
    {
        if (!Auth::checkRol()) {
            Request::redirect($url);
        }
    }
}