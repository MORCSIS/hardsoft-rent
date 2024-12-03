<?php
include 'connection.php';
require_once 'ValidaSesion.php';

try {
    $con = connection();
    // Capturamos el parámetro correcto de la URL
    $codigodebarras = isset($_GET['codigodebarras']) ? $_GET['codigodebarras'] : 0;

    if ($codigodebarras <= 0) {
        throw new Exception("Código de barras inválido");
    }

    // Preparamos la consulta para eliminar el registro usando el código de barras
    $stmt = $con->prepare("DELETE FROM materiales WHERE codigodebarras = ?");
    $stmt->bind_param("s", $codigodebarras); // Aquí usamos $codigodebarras, no $id_material
    $result = $stmt->execute();

    if ($result) {
        echo "<script>alert('Registro $codigodebarras eliminado'); window.location='Equipos.php';</script>";

//Ejecuta la funcion registerAction que se encuentra en el archivo ValidaSesion
$id_usuario = $_SESSION['id'];
$accion = "Borrado"; // Reemplaza con el tipo de acción (Creación, Actualización, Eliminación, Consulta, etc.)
$descripcion = "Borrado realizado en la página " . $currentPage;
registerAction($id_usuario, $accion, $descripcion);

} else {
        throw new Exception("No se pudo eliminar el registro $codigodebarras");
    }
} catch (Exception $e) {
    echo "<script>alert('Error: " . $e->getMessage() . "'); window.location='Equipos.php';</script>";
}
?>
