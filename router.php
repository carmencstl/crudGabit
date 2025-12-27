<?php
// Router para el servidor PHP built-in
// Permite servir archivos estáticos directamente

$requestUri = $_SERVER["REQUEST_URI"];
$requestUri = parse_url($requestUri, PHP_URL_PATH);

// Quitar /crudGabit del inicio si existe (para Railway)
if (str_starts_with($requestUri, "/crudGabit")) {
    $requestUri = substr($requestUri, strlen("/crudGabit"));
}

// Si el archivo existe físicamente en public/, servirlo
$filePath = __DIR__ . "/public" . $requestUri;

if (file_exists($filePath) && is_file($filePath)) {
    // Para archivos CSS, establecer el Content-Type correcto
    if (preg_match("/\.css$/i", $requestUri)) {
        header("Content-Type: text/css; charset=utf-8");
        readfile($filePath);
        exit;
    }
    // Para archivos JS
    if (preg_match("/\.js$/i", $requestUri)) {
        header("Content-Type: application/javascript; charset=utf-8");
        readfile($filePath);
        exit;
    }
    // Para imágenes y otros archivos estáticos
    if (preg_match("/\.(jpg|jpeg|png|gif|ico|svg|webp)$/i", $requestUri)) {
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

// Si no es un archivo estático, pasar al index.php
$_SERVER["SCRIPT_NAME"] = "/index.php";
require_once __DIR__ . "/public/index.php";
