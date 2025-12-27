<?php
/**
 * Router para PHP built-in server
 * CRÍTICO: Evitar bucles de redirección
 */

// Obtener la URI sin query string
$uri = urldecode(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH));

// IMPORTANTE: Si ya estamos procesando index.php, NO hacer nada más
if (strpos($_SERVER["SCRIPT_NAME"], "index.php") !== false) {
    return true;
}

// Construir path del archivo
$filepath = __DIR__ . $uri;

// Si es un archivo que existe (CSS, JS, imágenes), servirlo
if (is_file($filepath)) {
    return false; // PHP lo sirve automáticamente
}

// Si no es un archivo, cargar index.php UNA SOLA VEZ
$_SERVER["SCRIPT_NAME"] = "/index.php";
require_once __DIR__ . "/index.php";
exit(); // IMPORTANTE: terminar ejecución aquí
