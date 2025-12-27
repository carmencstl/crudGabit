<?php
// Router para archivos estáticos dentro de public/

error_log("=== ROUTER DEBUG START ===");
error_log("REQUEST_URI: " . $_SERVER["REQUEST_URI"]);

$requestUri = $_SERVER["REQUEST_URI"];
$requestUri = parse_url($requestUri, PHP_URL_PATH);

error_log("Parsed URI: " . $requestUri);

// Quitar /crudGabit del inicio si existe
if (str_starts_with($requestUri, "/crudGabit")) {
    $requestUri = substr($requestUri, strlen("/crudGabit"));
    error_log("After removing /crudGabit: " . $requestUri);
}

// Construir el path del archivo dentro de public/
$filePath = __DIR__ . $requestUri;
error_log("Looking for file: " . $filePath);
error_log("File exists: " . (file_exists($filePath) ? "YES" : "NO"));

if (file_exists($filePath) && is_file($filePath)) {
    error_log("File found! Serving: " . $filePath);
    
    // Determinar Content-Type
    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    
    $mimeTypes = [
        "css" => "text/css; charset=utf-8",
        "js" => "application/javascript; charset=utf-8",
        "jpg" => "image/jpeg",
        "jpeg" => "image/jpeg",
        "png" => "image/png",
        "gif" => "image/gif",
        "ico" => "image/x-icon",
        "svg" => "image/svg+xml",
        "webp" => "image/webp",
        "woff" => "font/woff",
        "woff2" => "font/woff2",
        "ttf" => "font/ttf",
        "eot" => "application/vnd.ms-fontobject",
        "otf" => "font/otf"
    ];
    
    $contentType = $mimeTypes[$extension] ?? "application/octet-stream";
    error_log("Content-Type: " . $contentType);
    
    header("Content-Type: " . $contentType);
    readfile($filePath);
    exit;
}

error_log("File not found, passing to index.php");
// Si no es un archivo estático, pasar al index.php
require_once __DIR__ . "/index.php";
