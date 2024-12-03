<?php
include 'connection.php';
require_once 'ValidaSesion.php';
$con = connection();
$con->set_charset("utf8");

session_start();

// Verificar si se ha proporcionado el ID en la URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('ID no proporcionado.'); window.location.href = 'catalogo_cuentas.php';</script>";
    exit();
}

$user_id = (int)$_GET['id'];

// Verificar que el usuario no esté intentando eliminar su propia cuenta
$loggedin_user_id = $_SESSION['id']; // Asegúrate de que la sesión contiene el ID del usuario

if ($user_id === $loggedin_user_id) {
    echo "<script>alert('No puedes eliminar tu propia cuenta.'); window.location.href = 'catalogo_cuentas.php';</script>";
    exit();
}

// Realizar la eliminación
$sql = "DELETE FROM accounts WHERE id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    echo "<script>alert('Cuenta eliminada exitosamente.'); window.location.href = 'accounts.php';</script>";

//Ejecuta la funcion registerAction que se encuentra en el archivo ValidaSesion
$id_usuario = $_SESSION['id'];
$accion = "Borrado"; // Reemplaza con el tipo de acción (Creación, Actualización, Eliminación, Consulta, etc.)
$descripcion = "Borrado realizado en la página " . $currentPage;
registerAction($id_usuario, $accion, $descripcion);


} else {
    echo "<script>alert('Error al eliminar la cuenta.'); window.history.back();</script>";
}

$stmt->close();
$con->close();
?>
