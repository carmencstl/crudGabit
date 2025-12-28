<?php
require_once __DIR__ . "/../vendor/autoload.php";

use CrudGabit\Config\DataBase;
use CrudGabit\Config\Request;
use CrudGabit\Modelos\Usuario;

echo "<h1>Test DELETE Simple</h1>";
echo "<pre>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "POST recibido!\n\n";
    
    $idUsuario = Request::post("idUsuario");
    echo "ID recibido del POST: " . ($idUsuario ?? "NULL") . "\n";
    echo "POST completo: " . print_r($_POST, true) . "\n\n";
    
    if ($idUsuario) {
        echo "Intentando borrar usuario ID: $idUsuario\n";
        
        try {
            $pdo = DataBase::connect();
            
            // Ver si el usuario existe
            $stmt = $pdo->prepare("SELECT * FROM usuario WHERE idUsuario = :id");
            $stmt->execute(['id' => $idUsuario]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($usuario) {
                echo "✅ Usuario encontrado: " . $usuario['nombre'] . " " . $usuario['apellidos'] . "\n\n";
                
                // Intentar borrar
                $stmt = $pdo->prepare("DELETE FROM usuario WHERE idUsuario = :id");
                $result = $stmt->execute(['id' => $idUsuario]);
                
                echo "Resultado execute: " . ($result ? "true" : "false") . "\n";
                echo "Filas afectadas: " . $stmt->rowCount() . "\n\n";
                
                if ($stmt->rowCount() > 0) {
                    echo "✅ USUARIO BORRADO EXITOSAMENTE\n";
                } else {
                    echo "⚠️ No se borró ninguna fila\n";
                }
            } else {
                echo "❌ Usuario con ID $idUsuario NO existe\n";
            }
            
        } catch (Exception $e) {
            echo "❌ ERROR: " . $e->getMessage() . "\n";
        }
    }
    
} else {
    // Mostrar formulario
    echo "Usuarios disponibles:\n\n";
    $pdo = DataBase::connect();
    $usuarios = $pdo->query("SELECT idUsuario, nombre, apellidos, email FROM usuario")->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($usuarios as $u) {
        echo "- ID: {$u['idUsuario']} | {$u['nombre']} {$u['apellidos']} ({$u['email']})\n";
    }
    
    echo "\n</pre>";
    echo '<form method="POST">
        <label>ID del usuario a borrar: <input type="number" name="idUsuario" required></label>
        <button type="submit">BORRAR</button>
    </form>';
}

echo "</pre>";
?>
