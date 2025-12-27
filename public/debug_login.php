<?php
require_once __DIR__ . "/../vendor/autoload.php";

use CrudGabit\Config\DataBase;

echo "<h1>Debug Login - Gabit</h1>";
echo "<pre>";

try {
    $pdo = DataBase::connect();
    
    echo "✅ Conectado a la base de datos\n\n";
    
    // Buscar el usuario admin
    $stmt = $pdo->prepare("SELECT * FROM usuario WHERE email = :email");
    $stmt->execute(['email' => 'admin@gabit.com']);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($usuario) {
        echo "👤 USUARIO ENCONTRADO:\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "ID: " . $usuario['idUsuario'] . "\n";
        echo "Username: " . $usuario['nombreUsuario'] . "\n";
        echo "Nombre: " . $usuario['nombre'] . " " . $usuario['apellidos'] . "\n";
        echo "Email: " . $usuario['email'] . "\n";
        echo "Rol: " . $usuario['rol'] . "\n";
        echo "Hash almacenado: " . substr($usuario['password'], 0, 50) . "...\n\n";
        
        // Probar contraseñas
        echo "🔐 PROBANDO CONTRASEÑAS:\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        
        $passwords = ['admin123', 'Admin123', 'admin', '123'];
        foreach ($passwords as $pass) {
            $verify = password_verify($pass, $usuario['password']);
            echo "Password '$pass': " . ($verify ? "✅ CORRECTA" : "❌ INCORRECTA") . "\n";
        }
        
        echo "\n";
        echo "💡 GENERAR NUEVO HASH:\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $newHash = password_hash('admin123', PASSWORD_DEFAULT);
        echo "Nuevo hash para 'admin123':\n";
        echo "$newHash\n\n";
        
        echo "Para actualizar la contraseña, ejecuta esto en Railway MySQL:\n";
        echo "UPDATE usuario SET password = '$newHash' WHERE email = 'admin@gabit.com';\n";
        
    } else {
        echo "❌ USUARIO NO ENCONTRADO\n\n";
        echo "El usuario admin@gabit.com NO existe en la base de datos.\n";
        echo "Ejecuta /insert_data.php primero para crearlo.\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "</pre>";
?>
