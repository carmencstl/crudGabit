<?php
// Archivo temporal para crear las tablas
// Bórralo después de usarlo por seguridad

require_once __DIR__ . "/../vendor/autoload.php";

use CrudGabit\Config\DataBase;

try {
    $pdo = DataBase::connect();
    
    echo "<h1>Creando tablas...</h1>";
    
    // Crear tabla usuarios
    $pdo->exec("CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        rol ENUM('admin', 'user') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    echo "<p>✅ Tabla 'usuarios' creada</p>";
    
    // Crear tabla habitos
    $pdo->exec("CREATE TABLE IF NOT EXISTS habitos (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    echo "<p>✅ Tabla 'habitos' creada</p>";
    
    // Crear tabla logros
    $pdo->exec("CREATE TABLE IF NOT EXISTS logros (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    echo "<p>✅ Tabla 'logros' creada</p>";
    
    // Insertar usuarios de prueba
    $pdo->exec("INSERT INTO usuarios (nombre, email, password, rol) VALUES 
        ('Admin', 'admin@gabit.com', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
        ON DUPLICATE KEY UPDATE nombre=nombre");
    
    echo "<p>✅ Usuario admin creado</p>";
    
    $pdo->exec("INSERT INTO usuarios (nombre, email, password, rol) VALUES 
        ('Usuario Demo', 'user@gabit.com', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user')
        ON DUPLICATE KEY UPDATE nombre=nombre");
    
    echo "<p>✅ Usuario demo creado</p>";
    
    echo "<h2>🎉 ¡TODO LISTO!</h2>";
    echo "<p><a href='/'>Ir al login</a></p>";
    echo "<p style='color: red;'>IMPORTANTE: Borra este archivo (public/setup.php) por seguridad</p>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
