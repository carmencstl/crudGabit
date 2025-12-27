<?php
// Router para el servidor PHP built-in
// Permite servir archivos estáticos directamente

error_log("=== ROUTER DEBUG START ===");
error_log("REQUEST_URI: " . $_SERVER["REQUEST_URI"]);

$requestUri = $_SERVER["REQUEST_URI"];
$requestUri = parse_url($requestUri, PHP_URL_PATH);

error_log("Parsed URI: " . $requestUri);

// Quitar /crudGabit del inicio si existe (para Railway)
if (str_starts_with($requestUri, "/crudGabit")) {
    $requestUri = substr($requestUri, strlen("/crudGabit"));
    error_log("After removing /crudGabit: " . $requestUri);
}

// Si el archivo existe físicamente en public/, servirlo
$filePath = __DIR__ . "/public" . $requestUri;
error_log("Looking for file: " . $filePath);
error_log("File exists: " . (file_exists($filePath) ? "YES" : "NO"));
error_log("Is file: " . (is_file($filePath) ? "YES" : "NO"));

if (file_exists($filePath) && is_file($filePath)) {
    error_log("Serving static file: " . $filePath);
    
    // Para archivos CSS, establecer el Content-Type correcto
    if (preg_match("/\.css$/i", $requestUri)) {
        error_log("Serving CSS file");
        header("Content-Type: text/css; charset=utf-8");
        readfile($filePath);
        exit;
    }
    // Para archivos JS
    if (preg_match("/\.js$/i", $requestUri)) {
        error_log("Serving JS file");
        header("Content-Type: application/javascript; charset=utf-8");
        readfile($filePath);
        exit;
    }
    // Para imágenes y otros archivos estáticos
    if (preg_match("/\.(jpg|jpeg|png|gif|ico|svg|webp)$/i", $requestUri)) {
        error_log("Serving image file");
        $mimeTypes = [
            "jpg" => "image/jpeg",
            "jpeg" => "image/jpeg",
            "png" => "image/png",
            "gif" => "image/gif",
            "ico" => "image/x-icon",
            "svg" => "image/svg+xml",
            "webp" => "image/webp"
        ];
        $ext = strtolower(pathinfo($requestUri, PATHINFO_EXTENSION));
        header("Content-Type: " . ($mimeTypes[$ext] ?? "application/octet-stream"));
        readfile($filePath);
        exit;
    }
    // Para fuentes
    if (preg_match("/\.(woff|woff2|ttf|eot|otf)$/i", $requestUri)) {
        error_log("Serving font file");
        $mimeTypes = [
            "woff" => "font/woff",
            "woff2" => "font/woff2",
            "ttf" => "font/ttf",
            "eot" => "application/vnd.ms-fontobject",
            "otf" => "font/otf"
        ];
        $ext = strtolower(pathinfo($requestUri, PATHINFO_EXTENSION));
        header("Content-Type: " . ($mimeTypes[$ext] ?? "application/octet-stream"));
        readfile($filePath);
        exit;
    }
}

error_log("Passing to index.php");
// Si no es un archivo estático, pasar al index.php
$_SERVER["SCRIPT_NAME"] = "/index.php";
require_once __DIR__ . "/public/index.php";
