<?php
include 'connection.php';
require_once 'ValidaSesion.php';

try {
    $con = connection();
    $con->set_charset("utf8");

    $id_marca = isset($_GET['id_marca']) ? (int)$_GET['id_marca'] : 0;

    if ($id_marca <= 0) {
        throw new Exception("ID de marca inválido");
    }

    $stmt = $con->prepare("DELETE FROM marcas WHERE id_marca = ?");
    $stmt->bind_param("i", $id_marca);
    $result = $stmt->execute();

    if ($result) {
        echo "<script>alert('Registro $id_marca eliminado'); window.location='marcas.php';</script>";
    
//Ejecuta la funcion registerAction que se encuentra en el archivo ValidaSesion
$id_usuario = $_SESSION['id'];
$accion = "Borrado"; // Reemplaza con el tipo de acción (Creación, Actualización, Eliminación, Consulta, etc.)
$descripcion = "Borrado realizado en la página " . $currentPage;
registerAction($id_usuario, $accion, $descripcion);
    
    } else {
        throw new Exception("No se pudo eliminar el registro");
    }
} catch (Exception $e) {
    echo "<script>alert('Error: " . $e->getMessage() . "'); window.location='marcas.php';</script>";
}
?>