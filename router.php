<?php
// Router para servidor PHP integrado
// Este archivo maneja archivos estáticos y rutas PHP

$uri = urldecode(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH));

// Si el archivo existe y es estático (CSS, JS, imágenes), servirlo directamente
if ($uri !== "/" && file_exists(__DIR__ . "/public" . $uri)) {
    return false; // Servir el archivo tal cual
}

// Si no, pasar al index.php
require_once __DIR__ . "/public/index.php";
