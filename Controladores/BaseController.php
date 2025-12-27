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
        
        // basePath vacío porque Apache apunta directo a /public
        $basePath = "";
        
        $this->twig->addGlobal("basePath", $basePath);
    }

    protected function render(string $template, array $info = []): string
    {
        $info["usuarioActivo"] = Auth::user();
        $info["isAdmin"] = Auth::checkRol();
        return $this->twig->render($template, $info);
    }
}
