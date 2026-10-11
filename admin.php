<?php
session_start();
require 'conexion.php';

// Validar que el usuario haya iniciado sesión y sea Administrador (rol 1)[cite: 4, 10]
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 1) {
    die("Acceso denegado. Esta página es solo para administradores.");
}

// Obtener los logs combinando la tabla accesos con usuarios
$stmt = $pdo->query("
    SELECT a.fecha_acceso, u.email, a.ip_usuario, a.resultado 
    FROM accesos a 
    JOIN usuarios u ON a.id_usuario = u.id_usuario 
    ORDER BY a.fecha_acceso DESC
");
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administrador</title>
</head>
<body>
    <h2>Log de Accesos (Auditoría)</h2>
    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>Fecha y Hora</th>
                <th>Usuario (Email)</th>
                <th>IP</th>
                <th>Resultado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $log): ?>
            <tr>
                <td><?php echo htmlspecialchars($log['fecha_acceso']); ?></td>
                <td><?php echo htmlspecialchars($log['email']); ?></td>
                <td><?php echo htmlspecialchars($log['ip_usuario']); ?></td>
                <td>
                    <?php 
                        if($log['resultado'] == 'exito') {
                            echo "<span style='color:green;'>Éxito</span>";
                        } else {
                            echo "<span style='color:red;'>Fallido</span>";
                        }
                    ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <br>
    <a href="logout.php">Cerrar Sesión</a>
</body>
</html>