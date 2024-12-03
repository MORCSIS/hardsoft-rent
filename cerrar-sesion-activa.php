<?php
include 'connection.php';

$conexion = connection();
$conexion->set_charset("utf8");

// Verificar que se envió el username
if (!isset($_GET['username'])) {
    header('Location: index.html');
    exit;
}

$username = $_GET['username'];

// Actualizar el session_token a NULL para el usuario
$stmt = $conexion->prepare('UPDATE accounts SET session_token = NULL WHERE username = ?');
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->close();

// Redirigir de vuelta a autenticacion.php para completar el inicio de sesión
header("Location: index.html");
exit;
?>