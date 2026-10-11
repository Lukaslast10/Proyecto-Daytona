<?php
session_start();
require __DIR__ . '/conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Verificar que nos hayan enviado un ID de curso por la URL
if (!isset($_GET['id'])) {
    die("Error: No se seleccionó ningún curso. <a href='dashboard.php'>Volver al inicio</a>");
}

$id_curso = $_GET['id'];

// Obtener la información del curso seleccionado
$stmtCurso = $pdo->prepare("SELECT * FROM cursos WHERE id_curso = ?");
$stmtCurso->execute([$id_curso]);
$cursoInfo = $stmtCurso->fetch(PDO::FETCH_ASSOC);

if (!$cursoInfo) {
    die("El curso no existe. <a href='dashboard.php'>Volver al inicio</a>");
}

// Obtener los módulos que pertenecen a este curso
$stmtModulos = $pdo->prepare("SELECT * FROM modulos WHERE id_curso = ?");
$stmtModulos->execute([$id_curso]);
$modulos = $stmtModulos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($cursoInfo['titulo']); ?></title>
</head>
<body>
    <a href="dashboard.php">← Volver a todos los cursos</a>
    
    <h2><?php echo htmlspecialchars($cursoInfo['titulo']); ?></h2>
    <p><em><?php echo htmlspecialchars($cursoInfo['descripcion']); ?></em></p>
    <hr>

    <h3>Módulos de estudio:</h3>
    <?php if (count($modulos) > 0): ?>
        <?php foreach ($modulos as $modulo): ?>
            <div style="background-color: #f9f9f9; padding: 10px; margin-bottom: 15px; border-left: 4px solid #007bff;">
                <h4><?php echo htmlspecialchars($modulo['titulo_modulo']); ?></h4>
                <p><?php echo nl2br(htmlspecialchars($modulo['contenido'])); ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Este curso aún no tiene módulos cargados.</p>
    <?php endif; ?>

</body>
</html>