<?php
include 'connection.php';
require_once 'ValidaSesion.php';

try {
    $con = connection();
    // Capturamos el parámetro correcto de la URL
    $id_cliente = isset($_GET['id_cliente']) ? intval($_GET['id_cliente']) : 0;

    if ($id_cliente <= 0) {
        throw new Exception("ID de cliente inválido");
    }

    // Preparamos la consulta para eliminar el registro usando el ID del cliente
    $stmt = $con->prepare("DELETE FROM clientes WHERE id_cliente = ?");
    $stmt->bind_param("i", $id_cliente); // Aquí usamos $id_cliente
    $result = $stmt->execute();

    if ($result) {
        echo "<script>alert('Registro con ID $id_cliente eliminado'); window.location='Clientes.php';</script>";
    
//Ejecuta la funcion registerAction que se encuentra en el archivo ValidaSesion
$id_usuario = $_SESSION['id'];
$accion = "Borrado"; // Reemplaza con el tipo de acción (Creación, Actualización, Eliminación, Consulta, etc.)
$descripcion = "Borrado realizado en la página " . $currentPage;
registerAction($id_usuario, $accion, $descripcion);

    
    } else {
        throw new Exception("No se pudo eliminar el registro con ID $id_cliente");
    }
} catch (Exception $e) {
    echo "<script>alert('Error: " . $e->getMessage() . "'); window.location='Clientes.php';</script>";
}
?>
