<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

echo "Testing error display...\n";

// Forzar un error para ver si se muestra
trigger_error("This is a test error", E_USER_WARNING);

echo "If you see this, error display is working.\n";

// Ahora probar cargar UserController
echo "\nTrying to load UserController...\n";
require_once __DIR__ . "/../vendor/autoload.php";

try {
    $controller = new CrudGabit\Controladores\UserController();
    echo "UserController loaded successfully!\n";
} catch (Error $e) {
    echo "ERROR loading UserController:\n";
    echo $e->getMessage() . "\n";
    echo $e->getFile() . ":" . $e->getLine() . "\n";
}
?>
