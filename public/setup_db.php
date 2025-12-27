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
            idUsuario INT NOT NULL AUTO_INCREMENT,
            nombreUsuario VARCHAR(50) NOT NULL,
            nombre VARCHAR(100) NOT NULL,
            apellidos VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            password VARCHAR(255) NOT NULL,
            rol ENUM('admin','usuario') DEFAULT 'usuario',
            fechaCreacion DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (idUsuario),
            UNIQUE KEY nombreUsuario (nombreUsuario),
            UNIQUE KEY email (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Tabla usuario creada\n\n";

    // Crear tabla camino
    echo "Creando tabla camino...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS camino (
            idCamino INT NOT NULL AUTO_INCREMENT,
            nombre VARCHAR(100) NOT NULL,
            descripcion TEXT NOT NULL,
            autor INT NOT NULL,
            categoria VARCHAR(50) NOT NULL,
            fechaCreacion DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (idCamino),
            KEY fk_camino_usuario (autor),
            CONSTRAINT fk_camino_usuario FOREIGN KEY (autor) REFERENCES usuario (idUsuario) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Tabla camino creada\n\n";

    // Crear tabla logro
    echo "Creando tabla logro...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS logro (
            idLogro INT NOT NULL AUTO_INCREMENT,
            nombre VARCHAR(100) NOT NULL,
            descripcion TEXT NOT NULL,
            idCamino INT NOT NULL,
            fechaCreacion TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (idLogro),
            KEY fk_logro_camino (idCamino),
            CONSTRAINT fk_logro_camino FOREIGN KEY (idCamino) REFERENCES camino (idCamino) ON DELETE CASCADE ON UPDATE CASCADE
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
