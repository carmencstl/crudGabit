<?php
echo "<h1>Test POST - Gabit</h1>";
echo "<pre>";

echo "Método: " . $_SERVER['REQUEST_METHOD'] . "\n\n";

echo "Datos GET:\n";
print_r($_GET);
echo "\n";

echo "Datos POST:\n";
print_r($_POST);
echo "\n";

echo "php://input:\n";
echo file_get_contents('php://input');
echo "\n";

echo "</pre>";

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo '<form method="POST" action="/test_post.php">
        <input type="text" name="test" value="valor de prueba">
        <input type="hidden" name="idUsuario" value="123">
        <button type="submit">Enviar POST</button>
    </form>';
}
?>
