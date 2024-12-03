<?php
include 'connection.php';
require_once 'ValidaSesion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogos: series</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>

<?php
try {
    $con = connection();
    $con->set_charset("utf8");

    $id_serie = isset($_GET['id_serie']) ? (int)$_GET['id_serie'] : 0;

    if ($id_serie <= 0) {
        throw new Exception("ID de serie inválido");
    }

    $stmt = $con->prepare("DELETE FROM series WHERE id_serie = ?");
    $stmt->bind_param("i", $id_serie);
    $result = $stmt->execute();

    if ($result) {
        echo "<script>alert('Registro $id_serie eliminado'); window.location='series.php';</script>";

 //Ejecuta la funcion registerAction que se encuentra en el archivo ValidaSesion
$id_usuario = $_SESSION['id'];
$accion = "Borrado"; // Reemplaza con el tipo de acción (Creación, Actualización, Eliminación, Consulta, etc.)
$descripcion = "Borrado realizado en la página " . $currentPage;
registerAction($id_usuario, $accion, $descripcion);   
    
    } else {
        throw new Exception("No se pudo eliminar el registro");
    }
} catch (Exception $e) {
    echo "<script>alert('Error: " . $e->getMessage() . "'); window.location='series.php';</script>";

}
?>
