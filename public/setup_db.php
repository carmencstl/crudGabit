<?php
// Script de configuración de base de datos para Railway
// Ejecuta este archivo UNA VEZ desde el navegador: https://tu-app.railway.app/setup_db.php
// Luego BÓRRALO por seguridad

echo "<h1>Configuración de Base de Datos - Gabit</h1>";
echo "<pre>";

try {
    // Leer variables de entorno
    $dbHost = $_ENV["DB_HOST"] ?? $_SERVER["DB_HOST"] ?? "localhost";
    $dbName = $_ENV["DB_NAME"] ?? $_SERVER["DB_NAME"] ?? "railway";
    $dbUser = $_ENV["DB_USER"] ?? $_SERVER["DB_USER"] ?? "root";
    $dbPass = $_ENV["DB_PASS"] ?? $_SERVER["DB_PASS"] ?? "";

    echo "Conectando a: $dbHost\n";
    echo "Base de datos: $dbName\n\n";

    $dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    echo "✅ Conexión exitosa!\n\n";

    // Crear tabla usuarios
    echo "Creando tabla usuarios...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            rol ENUM('admin', 'user') DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Tabla usuarios creada\n\n";

    // Crear tabla habitos
    echo "Creando tabla habitos...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS habitos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            descripcion TEXT,
            categoria VARCHAR(50),
            frecuencia VARCHAR(50),
            objetivo INT,
            usuario_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Tabla habitos creada\n\n";

    // Crear tabla logros
    echo "Creando tabla logros...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS logros (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            descripcion TEXT,
            icono VARCHAR(255),
            puntos INT DEFAULT 0,
            usuario_id INT NOT NULL,
            habito_id INT,
            conseguido BOOLEAN DEFAULT FALSE,
            fecha_conseguido TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            FOREIGN KEY (habito_id) REFERENCES habitos(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Tabla logros creada\n\n";

    // Insertar usuarios de prueba
    echo "Insertando usuarios de prueba...\n";
    $pdo->exec("
        INSERT IGNORE INTO usuarios (nombre, email, password, rol) VALUES 
        ('Admin', 'admin@gabit.com', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
        ('Usuario Demo', 'user@gabit.com', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user')
    ");
    echo "✅ Usuarios insertados\n\n";

    echo "========================================\n";
    echo "✅ ¡BASE DE DATOS CONFIGURADA!\n";
    echo "========================================\n\n";
    echo "Usuarios de prueba:\n";
    echo "- Admin: admin@gabit.com / admin123\n";
    echo "- User: user@gabit.com / user123\n\n";
    echo "⚠️ IMPORTANTE: Borra este archivo (setup_db.php) por seguridad.\n";

} catch (PDOException $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "</pre>";
?>
