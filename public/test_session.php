<?php
session_start();

if (!isset($_SESSION['test'])) {
    $_SESSION['test'] = 'Session working!';
    echo "Session created. Reload this page.";
} else {
    echo "Session works! Value: " . $_SESSION['test'];
    echo "<br>Session ID: " . session_id();
    echo "<br>Session save path: " . session_save_path();
}
?>
