<?php
// Router simplificado para archivos estáticos

$requestUri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

// NO quitar /crudGabit aquí - eso lo maneja el Router de la app
// Este router solo debe servir archivos estáticos que existen

// Buscar archivo en public/
$filePath = __DIR__ . $requestUri;

// Si es archivo y existe, servirlo INMEDIATAMENTE
if (is_file($filePath)) {
    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    
    $mimeTypes = [
        "css" => "text/css",
        "js" => "application/javascript",
        "png" => "image/png",
        "jpg" => "image/jpeg",
        "jpeg" => "image/jpeg",
        "gif" => "image/gif",
        "svg" => "image/svg+xml",
        "woff" => "font/woff",
        "woff2" => "font/woff2",
        "ttf" => "font/ttf"
    ];
    
    if (isset($mimeTypes[$ext])) {
        header("Content-Type: " . $mimeTypes[$ext]);
        readfile($filePath);
        die();
    }
}

// Si no es un archivo estático, pasar al index.php
require __DIR__ . "/index.php";
