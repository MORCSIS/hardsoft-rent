<?php
// Iniciar buffer de salida para prevenir cualquier salida no deseada
ob_start();

// Iniciar sesión
session_start();

// Función para manejar errores y redirigir
function handleErrorAndRedirect($error) {
    error_log($error);
    ob_end_clean();
    header('Location: index.html?error=' . urlencode('Error al cerrar sesión'));
    exit();
}

// Limpiar el token de sesión en la base de datos
if (isset($_SESSION['id'])) {
    try {
        require_once 'connection.php';
        $conexion = connection();
        
        if (!$conexion) {
            throw new Exception("Error de conexión a la base de datos");
        }

        $stmt = $conexion->prepare('UPDATE accounts SET session_token = "" WHERE id = ?');
        if (!$stmt) {
            throw new Exception("Error en la preparación de la consulta: " . $conexion->error);
        }

        $stmt->bind_param('i', $_SESSION['id']);
        
        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }

        if ($stmt->affected_rows == 0) {
            error_log("Advertencia: No se actualizó ningún registro para el ID " . $_SESSION['id']);
        }

        $stmt->close();
    } catch (Exception $e) {
        handleErrorAndRedirect("Error al limpiar el token de sesión: " . $e->getMessage());
    }
}

// Destruir la sesión
session_unset();
session_destroy();

// Limpiar el buffer de salida
ob_end_clean();

// Verificar si hay una redirección pendiente
if (isset($_GET['redirect']) && $_GET['redirect'] === 'autenticacion.php' && isset($_GET['username']) && isset($_GET['password'])) {
    $username = urlencode($_GET['username']);
    $password = urlencode($_GET['password']);
    header("Location: autenticacion.php?username=$username&password=$password");
} else {
    // Redirigir al usuario a la página de inicio de sesión
    header('Location: index.html');
}
exit();
?>