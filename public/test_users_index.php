<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test Users Index</h1>";
echo "<pre>";

try {
    require_once __DIR__ . "/../vendor/autoload.php";
    echo "✅ Autoload OK\n\n";
    
    use CrudGabit\Controladores\UserController;
    echo "✅ UserController cargado\n\n";
    
    $controller = new UserController();
    echo "✅ Instancia creada\n\n";
    
    echo "Intentando ejecutar index()...\n";
    $controller->index();
    
} catch (Throwable $e) {
    echo "❌ ERROR:\n";
    echo "Mensaje: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString();
}

echo "</pre>";
?>
