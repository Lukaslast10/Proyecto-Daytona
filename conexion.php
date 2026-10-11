<?php
$host = 'localhost';
$db = 'daytona_login';
$user = 'root'; 
$pass = ''; 

try {
    // Intentamos establecer la conexión con la base de datos
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    
    // Configuramos PDO para que nos muestre los errores si algo falla
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    // Si hay un error de conexión, detenemos la página y lo mostramos
    die("Error de conexión: " . $e->getMessage());
}
?>