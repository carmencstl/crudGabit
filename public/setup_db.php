<?php
echo "<h1>Configuración de Base de Datos - Gabit</h1>";
echo "<pre>";

try {
    $dbHost = $_ENV["DB_HOST"] ?? $_SERVER["DB_HOST"] ?? "localhost";
    $dbName = $_ENV["DB_NAME"] ?? $_SERVER["DB_NAME"] ?? "railway";
    $dbUser = $_ENV["DB_USER"] ?? $_SERVER["DB_USER"] ?? "root";
    $dbPass = $_ENV["DB_PASS"] ?? $_SERVER["DB_PASS"] ?? "";

    echo "Conectando a: $dbHost\n";
    echo "Base de datos: $dbName\n\n";

    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    echo "✅ Conexión exitosa!\n\n";

    // Crear tabla usuario
    echo "Creando tabla usuario...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS usuario (
            idUsuario INT AUTO_INCREMENT PRIMARY KEY,
            nombreUsuario VARCHAR(100) UNIQUE NOT NULL,
            nombre VARCHAR(100) NOT NULL,
            apellidos VARCHAR(100),
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            rol ENUM('admin', 'user') DEFAULT 'user',
            fechaCreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Tabla usuario creada\n\n";

    // Crear tabla camino
    echo "Creando tabla camino...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS camino (
            idCamino INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            descripcion TEXT,
            auto INT,
            categoria VARCHAR(50),
            fechaCreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Tabla camino creada\n\n";

    // Crear tabla logro
    echo "Creando tabla logro...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS logro (
            idLogro INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            descripcion TEXT,
            idCamino INT,
            fechaCreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (idCamino) REFERENCES camino(idCamino) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Tabla logro creada\n\n";

    // Insertar usuario admin
    echo "Creando usuario admin...\n";
    $pdo->exec("
        INSERT IGNORE INTO usuario (nombreUsuario, nombre, apellidos, email, password, rol) VALUES 
        ('admin', 'Admin', 'Gabit', 'admin@gabit.com', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
    ");
    echo "✅ Usuario admin creado\n\n";

    echo "========================================\n";
    echo "✅ ¡BASE DE DATOS CONFIGURADA!\n";
    echo "========================================\n\n";
    echo "Usuario de prueba:\n";
    echo "- Email: admin@gabit.com\n";
    echo "- Password: admin123\n\n";
    echo "⚠️ BORRA este archivo por seguridad.\n";

} catch (PDOException $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "</pre>";
?>
