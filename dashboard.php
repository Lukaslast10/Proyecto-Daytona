<?php
session_start();
require __DIR__ . '/conexion.php';

// Validar que el usuario haya iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Obtener todos los cursos de la base de datos
$stmt = $pdo->query("SELECT * FROM cursos");
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Cursos - Phishing Awareness</title>
</head>
<body>
    <h2>Capacitación de Seguridad: Prevención de Phishing</h2>
    <p>Selecciona un curso para comenzar tu capacitación.</p>
    
    <div class="lista-cursos">
        <?php foreach ($cursos as $curso): ?>
            <div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 10px;">
                <h3><?php echo htmlspecialchars($curso['titulo']); ?></h3>
                <p><?php echo htmlspecialchars($curso['descripcion']); ?></p>
                <!-- Este enlace llevará al usuario a ver los detalles del curso -->
                <a href="ver_curso.php?id=<?php echo $curso['id_curso']; ?>">Ver Módulos del Curso</a>
            </div>
        <?php endforeach; ?>
    </div>

    <br>
    <a href="logout.php">Cerrar Sesión</a>
</body>
</html>
