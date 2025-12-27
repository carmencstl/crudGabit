<?php
/**
 * Router para PHP built-in server (Railway)
 * Equivalente al .htaccess pero para el servidor de desarrollo de PHP
 * 
 * Lógica: Si el archivo existe, déjalo pasar (return false)
 *         Si no existe, carga index.php
 */

$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$filePath = __DIR__ . $uri;

// Si el archivo existe (CSS, JS, imágenes, etc), servirlo directamente
if (file_exists($filePath) && is_file($filePath)) {
    return false; // El servidor PHP lo servirá automáticamente
}

// Si no existe, pasar al index.php
require_once __DIR__ . "/index.php";
