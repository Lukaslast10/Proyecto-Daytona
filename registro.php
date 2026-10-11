<?php
require 'conexion.php';
$mensaje = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validación de seguridad de la contraseña
    // Regex: Mínimo 8 caracteres, al menos 1 letra mayúscula, 1 número y 1 símbolo
    if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
        $mensaje = "La contraseña debe tener mínimo 8 caracteres, una mayúscula, un número y un símbolo.";
    } else {
        // Verificar si el correo electrónico ya existe (único)
        $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $mensaje = "El correo electrónico ya se encuentra registrado.";
        } else {
            // Encriptar la contraseña por seguridad
            $hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Insertar usuario (por defecto tomará el id_rol 2 que es 'usuario')
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre_usuario, email, clave) VALUES (?, ?, ?)");
            
            if ($stmt->execute([$nombre, $email, $hash])) {
                $mensaje = "Registro exitoso. <a href='login.php'>Inicia sesión aquí</a>.";
            } else {
                $mensaje = "Ocurrió un error al registrar el usuario.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Phishing Awareness</title>
</head>
<body>
    <h2>Registro de Usuario</h2>
    <?php if($mensaje != '') echo "<p><strong>$mensaje</strong></p>"; ?>
    <form method="POST" action="registro.php">
        <label>Nombre:</label><br>
        <input type="text" name="nombre" required><br><br>
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>
        <label>Contraseña:</label><br>
        <input type="password" name="password" required><br><br>
        <button type="submit">Registrarse</button>
    </form>
</body>
</html>