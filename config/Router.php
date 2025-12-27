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
     * Mostrar error 404 SIN REDIRECT (evita bucles infinitos)
     * @return void
     */
    private function error404(): void
    {
        http_response_code(404);
        echo "<!DOCTYPE html>
<html>
<head>
    <title>404 - Página no encontrada</title>
    <style>
        body { 
            font-family: 'Segoe UI', Arial, sans-serif; 
            text-align: center; 
            padding: 50px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 500px;
            margin: 0 auto;
        }
        h1 { 
            color: #05576B;
            margin-bottom: 20px;
        }
        p {
            color: #666;
            margin-bottom: 30px;
        }
        a {
            background: #05576B;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
        }
        a:hover {
            background: #0E3B47;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>404</h1>
        <p>La página que buscas no existe.</p>
        <a href='" . $this->basePath . "/dashboard'>Volver al Dashboard</a>
    </div>
</body>
</html>";
        exit();
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
