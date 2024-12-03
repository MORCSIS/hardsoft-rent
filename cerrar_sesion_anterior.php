<?php
include 'connection.php';
$conexion = connection();
session_start();

// Verificar que se haya recibido el nombre de usuario
if (!isset($_POST['username'])) {
    echo "<script>alert('Error: No se recibió el nombre de usuario.'); window.location='index.html';</script>";
    exit;
}

$username = $_POST['username'];

// Generar un nuevo session_token
$new_token = bin2hex(random_bytes(32));

// Actualizar el session_token en la base de datos para cerrar la sesión anterior
if ($stmt = $conexion->prepare('UPDATE accounts SET session_token = ? WHERE username = ?')) {
    $stmt->bind_param('ss', $new_token, $username);
    if ($stmt->execute()) {
        // Invalida cualquier sesión anterior
        session_regenerate_id();

        // Iniciar nueva sesión y establecer los parámetros
        $stmt_select = $conexion->prepare('SELECT id, name, rol, activo, fecha_creacion FROM accounts WHERE username = ?');
        $stmt_select->bind_param('s', $username);
        $stmt_select->execute();
        $stmt_select->store_result();

        if ($stmt_select->num_rows > 0) {
            $stmt_select->bind_result($id, $name, $rol, $activo, $fecha_creacion);
            $stmt_select->fetch();

            // Establecer las variables de sesión
            $_SESSION['loggedin'] = true;
            $_SESSION['name'] = $name;
            $_SESSION['id'] = $id;
            $_SESSION['rol'] = $rol;
            $_SESSION['activo'] = $activo;
            $_SESSION['fecha_creacion'] = $fecha_creacion;
            $_SESSION['session_token'] = $new_token;

            // Redirigir al usuario al inicio
            header('Location: Inicio.php');
            exit;
        } else {
            echo "<script>alert('Error: No se encontró el usuario.'); window.location='index.html';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Error al actualizar el session_token.'); window.location='index.html';</script>";
        exit;
    }
    $stmt->close();
} else {
    echo "<script>alert('Error: No se pudo preparar la consulta.'); window.location='index.html';</script>";
    exit;
}
?>
