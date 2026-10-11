<?php
session_start();
// Si no hay una sesión activa, lo pateamos al login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Acá el de frontend pondrá su HTML, vos solo le dejás la lógica lista
echo "Bienvenido al sistema de concientización de Phishing.";
echo "<br><a href='logout.php'>Cerrar sesión</a>";
?>