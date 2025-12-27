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
        
        // Detectar automáticamente el basePath
        $basePath = getenv("BASE_PATH");
        
        // Si no está definido, detectar automáticamente
        if ($basePath === false || $basePath === null) {
            // En Railway, RAILWAY_ENVIRONMENT existe
            if (getenv("RAILWAY_ENVIRONMENT") !== false) {
                $basePath = "";  // Railway: sin subdirectorio
            } else {
                $basePath = "/crudGabit";  // Local: con subdirectorio
            }
        }
        
        error_log("BaseController: basePath configurado como: '" . $basePath . "'");
        
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
