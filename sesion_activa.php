<?php
session_start();
require_once 'ValidaSesion.php';

$username = $_SESSION['name'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesión Activa</title>
</head>
<body>
    <h1>Sesión Activa Detectada</h1>
    <p>Ya tienes una sesión activa en otro dispositivo. ¿Deseas cerrarla para iniciar aquí?</p>

    <form method="post" action="cerrar_sesion_anterior.php">
        <input type="hidden" name="username" value="<?php echo htmlspecialchars($username); ?>">
        <button type="submit" name="cerrar">Sí, cerrar sesión anterior</button>
        <a href="index.html">No, regresar al inicio</a>
    </form>
</body>
</html>
