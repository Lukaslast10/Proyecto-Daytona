<?php
session_start();
require 'conexion.php';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $ip = $_SERVER['REMOTE_ADDR']; // Obtener IP del usuario

    // Buscar el usuario por email
    $stmt = $pdo->prepare("SELECT id_usuario, clave, id_rol FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['clave'])) {
        // Credenciales correctas: Guardar log de ÉXITO[cite: 10]
        $log = $pdo->prepare("INSERT INTO accesos (id_usuario, ip_usuario, resultado) VALUES (?, ?, 'exito')");
        $log->execute([$user['id_usuario'], $ip]);

        // Crear variables de sesión
        $_SESSION['usuario_id'] = $user['id_usuario'];
        $_SESSION['rol_id'] = $user['id_rol'];

        // Redirigir según el rol
        if ($user['id_rol'] == 1) { // 1 = Admin[cite: 10]
            header("Location: admin.php");
        } else { // 2 = Usuario[cite: 10]
            header("Location: dashboard.php");
        }
        exit;
    } else {
        // Credenciales incorrectas[cite: 4]
        // Si el usuario existe, pero puso mal la clave, guardamos el log de FALLO
        if ($user) {
            $log = $pdo->prepare("INSERT INTO accesos (id_usuario, ip_usuario, resultado) VALUES (?, ?, 'fallido')");
            $log->execute([$user['id_usuario'], $ip]);
        }
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Phishing Awareness</title>
</head>
<body>
    <h2>Iniciar Sesión</h2>
    <?php if($error != '') echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST" action="login.php">
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>
        <label>Contraseña:</label><br>
        <input type="password" name="password" required><br><br>
        <button type="submit">Ingresar</button>
    </form>
    <p><a href="registro.php">Crear una cuenta</a></p>
</body>
</html>