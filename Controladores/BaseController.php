<?php

namespace CrudGabit\Controladores;

use CrudGabit\Config\Auth;

abstract class BaseController
{
    protected $twig;

    public function __construct()
    {
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . "/../Vistas");
        $this->twig = new \Twig\Environment($loader);
        
        // Añadir basePath como variable global
        $basePath = getenv("BASE_PATH") ?: "/crudGabit";
        $this->twig->addGlobal("basePath", $basePath);
    }

    /**
     * Renderizar plantilla con datos adicionales
     * @param string $template
     * @param array $data
     * @return string
     */
    protected function render(string $template, array $info = []): string
    {
        $info["usuarioActivo"] = Auth::user();
        $info["isAdmin"] = Auth::checkRol();
        return $this->twig->render($template, $info);
    }
}
