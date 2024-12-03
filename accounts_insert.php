<?php

include 'connection.php';
require_once 'ValidaSesion.php';
$con = connection();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = strtoupper($con->real_escape_string($_POST['username']));
    $email = strtoupper($con->real_escape_string($_POST['email']));
    $name = strtoupper($con->real_escape_string($_POST['name']));
    $apaterno = strtoupper($con->real_escape_string($_POST['apaterno']));
    $amaterno = strtoupper($con->real_escape_string($_POST['amaterno']));
    $rol = (int)$_POST['rol'];
    
    $password = $con->real_escape_string($_POST['password']);

    // Verificar si el username ya existe
    $sql_check_username = "SELECT * FROM accounts WHERE username = ?";
    $stmt_check = $con->prepare($sql_check_username);
    $stmt_check->bind_param("s", $username);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // El username ya existe
        echo "<script>alert('El nombre de usuario ya existe. Por favor, elige otro.'); window.history.back();</script>";
        exit();
    }

    // Si el nombre de usuario no existe, proceder a insertar el nuevo usuario
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql_insert = "INSERT INTO accounts (username, email, name, apaterno, amaterno, rol, password) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = $con->prepare($sql_insert);
    $stmt_insert->bind_param("sssssis", $username, $email, $name, $apaterno, $amaterno, $rol, $hashed_password);

    if ($stmt_insert->execute()) {
        echo "<script>alert('Usuario "  . $username .  "  creado exitosamente'); window.location.href = 'accounts.php';</script>";
        
    //Ejecuta la funcion registerAction que se encuentra en el archivo ValidaSesion
    $id_usuario = $_SESSION['id'];
    $accion = "Creación"; // Reemplaza con el tipo de acción (Creación, Actualización, Eliminación, Consulta, etc.)
    $descripcion = "Creación realizada en la página " . $currentPage;
    registerAction($id_usuario, $accion, $descripcion);
        
    } else {
        echo "<script>alert('Error al crear el usuario'); window.history.back();</script>";
    }
    
    $stmt_insert->close();
    $stmt_check->close();
    $con->close();
}
?>
