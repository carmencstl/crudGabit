<?php
echo "<h1>Borrar Tablas - Gabit</h1>";
echo "<pre>";

try {
    $dbHost = $_ENV["DB_HOST"] ?? $_SERVER["DB_HOST"] ?? "localhost";
    $dbName = $_ENV["DB_NAME"] ?? $_SERVER["DB_NAME"] ?? "railway";
    $dbUser = $_ENV["DB_USER"] ?? $_SERVER["DB_USER"] ?? "root";
    $dbPass = $_ENV["DB_PASS"] ?? $_SERVER["DB_PASS"] ?? "";

    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    echo "Borrando tablas...\n\n";
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("DROP TABLE IF EXISTS logro");
    echo "✅ Tabla logro borrada\n";
    
    $pdo->exec("DROP TABLE IF EXISTS camino");
    echo "✅ Tabla camino borrada\n";
    
    $pdo->exec("DROP TABLE IF EXISTS usuario");
    echo "✅ Tabla usuario borrada\n";
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    echo "\n========================================\n";
    echo "✅ ¡TABLAS BORRADAS!\n";
    echo "========================================\n\n";
    echo "Ahora accede a /setup_db.php para recrearlas.\n";

} catch (PDOException $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "</pre>";
?>
