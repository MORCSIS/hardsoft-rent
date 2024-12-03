<?php
include 'connection.php';
require_once 'ValidaSesion.php';
$con = connection();
$con->set_charset("utf8");


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = strtoupper($con->real_escape_string($_POST['username']));
    $email = strtoupper($con->real_escape_string($_POST['email']));
    $name = strtoupper($con->real_escape_string($_POST['name']));
    $apaterno = strtoupper($con->real_escape_string($_POST['apaterno']));
    $amaterno = strtoupper($con->real_escape_string($_POST['amaterno']));
    $rol = (int)$_POST['rol'];
    
    $password = $con->real_escape_string($_POST['password']);

    if (!empty($password)) {
        // Si la contraseña fue cambiada, la actualizamos
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql_update = "UPDATE accounts SET email = ?, name = ?, apaterno = ?, amaterno = ?, rol = ?, password = ? WHERE username = ?";
        $stmt_update = $con->prepare($sql_update);
        $stmt_update->bind_param("ssssiss", $email, $name, $apaterno, $amaterno, $rol, $hashed_password, $username);
    } else {
        // Si no se cambió la contraseña, la dejamos igual
        $sql_update = "UPDATE accounts SET email = ?, name = ?, apaterno = ?, amaterno = ?, rol = ? WHERE username = ?";
        $stmt_update = $con->prepare($sql_update);
        $stmt_update->bind_param("ssssis", $email, $name, $apaterno, $amaterno, $rol, $username);
    }

    if ($stmt_update->execute()) {
        echo "<script>alert('Usuario " . $username . " actualizado exitosamente'); window.location.href = 'Inicio.php';</script>";
   
//Ejecuta la funcion registerAction que se encuentra en el archivo ValidaSesion
$id_usuario = $_SESSION['id'];
$accion = "Actualización"; // Reemplaza con el tipo de acción (Creación, Actualización, Eliminación, Consulta, etc.)
$descripcion = "Actualización realizada en la página " . $currentPage;
registerAction($id_usuario, $accion, $descripcion);   
   
    } else {
        echo "<script>alert('Error al actualizar el usuario'); window.history.back();</script>";
    }

    $stmt_update->close();
    $con->close();
}
?>
