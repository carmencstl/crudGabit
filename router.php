<?php
// Router para el servidor PHP built-in
// Permite servir archivos estáticos directamente

$requestUri = $_SERVER['REQUEST_URI'];
$requestUri = parse_url($requestUri, PHP_URL_PATH);

// Si el archivo existe físicamente, servirlo
if (file_exists(__DIR__ . '/public' . $requestUri)) {
    // Para archivos CSS, establecer el Content-Type correcto
    if (preg_match('/\.css$/', $requestUri)) {
        header('Content-Type: text/css');
        readfile(__DIR__ . '/public' . $requestUri);
        exit;
    }
    // Para archivos JS
    if (preg_match('/\.js$/', $requestUri)) {
        header('Content-Type: application/javascript');
        readfile(__DIR__ . '/public' . $requestUri);
        exit;
    }
    // Para imágenes y otros archivos estáticos
    if (preg_match('/\.(jpg|jpeg|png|gif|ico|svg|woff|woff2|ttf|eot)$/', $requestUri)) {
        return false; // Dejar que PHP sirva el archivo automáticamente
    }
}

// Si no es un archivo estático, pasar al index.php
require_once __DIR__ . '/public/index.php';
