<?php
include 'connection.php';
require_once 'ValidaSesion.php';

try {
    $con = connection();
    $con->set_charset("utf8mb4");  // Set charset to UTF-8
    
    $id_disco = isset($_GET['id_disco']) ? (int)$_GET['id_disco'] : 0;

    if ($id_disco <= 0) {
        throw new Exception("ID de disco inválido");
    }

    $stmt = $con->prepare("DELETE FROM discos WHERE id_disco = ?");
    $stmt->bind_param("i", $id_disco);
    $result = $stmt->execute();

    if ($result) {
        echo "<script>alert('Registro $id_disco eliminado'); window.location='discos.php';</script>";
   
//Ejecuta la funcion registerAction que se encuentra en el archivo ValidaSesion
$id_usuario = $_SESSION['id'];
$accion = "Borrado"; // Reemplaza con el tipo de acción (Creación, Actualización, Eliminación, Consulta, etc.)
$descripcion = "Borrado realizado en la página " . $currentPage;
registerAction($id_usuario, $accion, $descripcion);
   
    } else {
        throw new Exception("No se pudo eliminar el registro");
    }
} catch (Exception $e) {
    echo "<script>alert('Error: " . addslashes($e->getMessage()) . "'); window.location='discos.php';</script>";
}
?>