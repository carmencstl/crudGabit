<?php
require_once __DIR__ . "/../vendor/autoload.php";

use CrudGabit\Config\DataBase;

echo "<h1>Actualizar Contraseña Admin - Gabit</h1>";
echo "<pre>";

try {
    $pdo = DataBase::connect();
    
    echo "✅ Conectado a la base de datos\n\n";
    
    // Generar hash correcto para admin123
    $newHash = password_hash('admin123', PASSWORD_DEFAULT);
    
    echo "Generando nuevo hash para 'admin123'...\n";
    echo "Hash: $newHash\n\n";
    
    // Actualizar la contraseña
    $stmt = $pdo->prepare("UPDATE usuario SET password = :password WHERE email = 'admin@gabit.com'");
    $stmt->execute(['password' => $newHash]);
    
    echo "✅ Contraseña actualizada!\n\n";
    
    // Verificar que funciona
    $stmt = $pdo->prepare("SELECT * FROM usuario WHERE email = 'admin@gabit.com'");
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $verify = password_verify('admin123', $usuario['password']);
    
    echo "🔐 VERIFICACIÓN:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Password 'admin123': " . ($verify ? "✅ CORRECTA" : "❌ INCORRECTA") . "\n\n";
    
    if ($verify) {
        echo "========================================\n";
        echo "✅ ¡TODO LISTO!\n";
        echo "========================================\n\n";
        echo "Ahora puedes hacer login con:\n";
        echo "Email: admin@gabit.com\n";
        echo "Password: admin123\n\n";
        echo "⚠️ Borra este archivo (fix_admin_password.php) por seguridad.\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "</pre>";
?>
