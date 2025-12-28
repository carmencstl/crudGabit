<?php
echo "<h1>UserController Log</h1>";
echo "<pre>";

$logFile = '/tmp/user_controller.log';

if (file_exists($logFile)) {
    echo file_get_contents($logFile);
} else {
    echo "No log file found yet. Try using the CRUD first.";
}

echo "</pre>";

echo '<br><a href="/users">Go to Users CRUD</a>';
echo ' | <a href="/view_log.php">Refresh Log</a>';
?>
