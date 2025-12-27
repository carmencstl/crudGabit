<?php
echo "<h1>Insertar Datos de Ejemplo - Gabit</h1>";
echo "<pre>";

try {
    $dbHost = $_ENV["DB_HOST"] ?? $_SERVER["DB_HOST"] ?? "localhost";
    $dbName = $_ENV["DB_NAME"] ?? $_SERVER["DB_NAME"] ?? "railway";
    $dbUser = $_ENV["DB_USER"] ?? $_SERVER["DB_USER"] ?? "root";
    $dbPass = $_ENV["DB_PASS"] ?? $_SERVER["DB_PASS"] ?? "";

    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    echo "✅ Conectado a la base de datos\n\n";

    // Generar hash correcto para admin123
    $passwordHash = password_hash("admin123", PASSWORD_DEFAULT);
    echo "Hash generado para 'admin123'\n\n";

    // Insertar usuarios
    echo "Insertando usuarios...\n";
    
    // Administrador
    $stmt = $pdo->prepare("INSERT IGNORE INTO usuario (nombreUsuario, nombre, apellidos, email, password, rol) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute(['admin', 'Carmen', 'Castillo', 'admin@gabit.com', $passwordHash, 'admin']);
    echo "  ✓ Usuario admin creado (email: admin@gabit.com, password: admin123)\n";

    // Usuarios normales
    $usuarios = [
        ['juanperez', 'Juan', 'Pérez García', 'juan@ejemplo.com'],
        ['mariagomez', 'María', 'Gómez López', 'maria@ejemplo.com'],
        ['pedromart', 'Pedro', 'Martínez Ruiz', 'pedro@ejemplo.com'],
        ['anafernandez', 'Ana', 'Fernández Sánchez', 'ana@ejemplo.com'],
        ['carlosdiaz', 'Carlos', 'Díaz Moreno', 'carlos@ejemplo.com'],
        ['lauralopez', 'Laura', 'López Jiménez', 'laura@ejemplo.com'],
        ['davidgonzalez', 'David', 'González Muñoz', 'david@ejemplo.com'],
        ['soniatorres', 'Sonia', 'Torres Romero', 'sonia@ejemplo.com']
    ];

    $userIds = [];
    foreach ($usuarios as $user) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO usuario (nombreUsuario, nombre, apellidos, email, password, rol) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $user[0],
            $user[1],
            $user[2],
            $user[3],
            $passwordHash,
            'usuario'
        ]);
        
        // Obtener el ID insertado
        $result = $pdo->query("SELECT idUsuario FROM usuario WHERE email = '{$user[3]}'")->fetch();
        if ($result) {
            $userIds[] = $result['idUsuario'];
        }
    }
    echo "  ✓ 8 usuarios normales creados\n\n";

    if (count($userIds) < 8) {
        echo "⚠️  No hay suficientes usuarios para crear caminos.\n";
        exit;
    }

    echo "Insertando caminos (hábitos)...\n";
    
    // Caminos variados
    $caminos = [
        ['Meditación Diaria', 'Practicar meditación mindfulness 10 minutos cada mañana', $userIds[0], 'Salud Mental'],
        ['Ejercicio Matutino', 'Hacer 30 minutos de ejercicio antes de desayunar', $userIds[1], 'Salud Física'],
        ['Lectura Nocturna', 'Leer al menos 20 páginas antes de dormir', $userIds[2], 'Desarrollo Personal'],
        ['Dieta Saludable', 'Consumir 5 porciones de frutas y verduras al día', $userIds[3], 'Nutrición'],
        ['Aprender Inglés', 'Estudiar inglés 30 minutos diarios con Duolingo', $userIds[4], 'Educación'],
        ['Yoga Semanal', 'Practicar yoga 3 veces por semana', $userIds[0], 'Salud Física'],
        ['Journaling', 'Escribir en mi diario personal cada noche', $userIds[1], 'Desarrollo Personal'],
        ['Hidratación', 'Beber 2 litros de agua al día', $userIds[2], 'Salud Física'],
        ['Cocinar en Casa', 'Preparar comidas caseras 5 días a la semana', $userIds[3], 'Nutrición'],
        ['Caminar', 'Caminar 10,000 pasos diarios', $userIds[4], 'Salud Física'],
        ['Programación', 'Practicar código 1 hora diaria', $userIds[5], 'Desarrollo Profesional'],
        ['Gratitud', 'Escribir 3 cosas por las que estoy agradecido cada día', $userIds[6], 'Salud Mental'],
        ['Desconexión Digital', 'No usar el móvil 1 hora antes de dormir', $userIds[7], 'Salud Mental'],
        ['Networking', 'Contactar con un profesional nuevo cada semana', $userIds[5], 'Desarrollo Profesional'],
        ['Ahorro', 'Ahorrar 10% del salario mensual', $userIds[6], 'Finanzas']
    ];

    $caminoIds = [];
    foreach ($caminos as $camino) {
        $stmt = $pdo->prepare("INSERT INTO camino (nombre, descripcion, autor, categoria) VALUES (?, ?, ?, ?)");
        $stmt->execute($camino);
        $caminoIds[] = $pdo->lastInsertId();
    }
    echo "  ✓ " . count($caminos) . " caminos creados\n\n";

    echo "Insertando logros...\n";
    
    // Logros para algunos caminos
    $logros = [
        ['Primera Meditación', 'Completaste tu primera sesión de meditación', $caminoIds[0]],
        ['Semana Completa', 'Meditaste 7 días seguidos', $caminoIds[0]],
        ['Mes de Constancia', 'Un mes completo de meditación diaria', $caminoIds[0]],
        
        ['Primera Carrera', 'Completaste tu primer entrenamiento matutino', $caminoIds[1]],
        ['Madrugador', '5 días seguidos de ejercicio matutino', $caminoIds[1]],
        
        ['Primer Libro', 'Terminaste tu primer libro del año', $caminoIds[2]],
        ['Lector Ávido', 'Leíste 10 libros este año', $caminoIds[2]],
        
        ['Día Verde', 'Cumpliste tu meta de frutas y verduras', $caminoIds[3]],
        ['Semana Saludable', '7 días seguidos de alimentación saludable', $caminoIds[3]],
        
        ['Primera Lección', 'Completaste tu primera lección de inglés', $caminoIds[4]],
        ['Racha de 7 Días', 'Estudiaste inglés 7 días seguidos', $caminoIds[4]],
        
        ['Primera Postura', 'Asististe a tu primera clase de yoga', $caminoIds[5]],
        ['Yogui Dedicado', 'Completaste 12 sesiones de yoga', $caminoIds[5]],
        
        ['Primer Entrada', 'Escribiste tu primera entrada en el diario', $caminoIds[6]],
        ['Mes de Reflexión', 'Un mes completo escribiendo en tu diario', $caminoIds[6]],
        
        ['Hidratado', 'Bebiste 2 litros de agua hoy', $caminoIds[7]],
        ['Semana Hidratada', '7 días seguidos alcanzando tu meta de agua', $caminoIds[7]],
        
        ['Chef Casero', 'Cocinaste 5 comidas en casa esta semana', $caminoIds[8]],
        ['Maestro de Cocina', 'Un mes completo cocinando en casa', $caminoIds[8]],
        
        ['10K Pasos', 'Alcanzaste 10,000 pasos hoy', $caminoIds[9]],
        ['Caminante Constante', '30 días seguidos de 10K pasos', $caminoIds[9]]
    ];

    foreach ($logros as $logro) {
        $stmt = $pdo->prepare("INSERT INTO logro (nombre, descripcion, idCamino) VALUES (?, ?, ?)");
        $stmt->execute($logro);
    }
    echo "  ✓ " . count($logros) . " logros creados\n\n";

    echo "========================================\n";
    echo "✅ ¡DATOS INSERTADOS EXITOSAMENTE!\n";
    echo "========================================\n\n";
    
    echo "USUARIOS CREADOS:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "👑 ADMINISTRADOR:\n";
    echo "   Email: admin@gabit.com\n";
    echo "   Password: admin123\n\n";
    
    echo "👥 USUARIOS NORMALES (todos con password: admin123):\n";
    echo "   • juan@ejemplo.com (Juan Pérez)\n";
    echo "   • maria@ejemplo.com (María Gómez)\n";
    echo "   • pedro@ejemplo.com (Pedro Martínez)\n";
    echo "   • ana@ejemplo.com (Ana Fernández)\n";
    echo "   • carlos@ejemplo.com (Carlos Díaz)\n";
    echo "   • laura@ejemplo.com (Laura López)\n";
    echo "   • david@ejemplo.com (David González)\n";
    echo "   • sonia@ejemplo.com (Sonia Torres)\n\n";
    
    echo "📊 ESTADÍSTICAS:\n";
    echo "   • " . (count($userIds) + 1) . " usuarios\n";
    echo "   • " . count($caminos) . " caminos (hábitos)\n";
    echo "   • " . count($logros) . " logros\n\n";
    
    echo "⚠️  IMPORTANTE: Borra este archivo (insert_data.php) por seguridad.\n";

} catch (PDOException $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "</pre>";
?>
