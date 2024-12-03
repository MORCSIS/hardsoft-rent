<?php
include 'db_config.php';

function connection() {
    // Crear una conexión a la base de datos con mysqli
    $connect = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Verificar si la conexión fue exitosa
    if ($connect->connect_error) {
        die('Error de conexión: ' . $connect->connect_error);
    }

    // Establecer la codificación de caracteres a UTF-8
    $connect->set_charset("utf8");

    return $connect;
}

function registerAction($id_usuario, $accion, $descripcion) {
    $conexion = connection();

    $sql = "INSERT INTO acciones (id_usuario, accion, descripcion) VALUES (?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("iss", $id_usuario, $accion, $descripcion);

    if ($stmt->execute()) {
        // La acción se registró correctamente
    } else {
        // Hubo un error al registrar la acción
        error_log("Error al registrar la acción: " . $stmt->error);
    }

    $stmt->close();
    $conexion->close();
}
?>
