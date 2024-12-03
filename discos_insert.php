<?php
include('connection.php');
require_once 'ValidaSesion.php';
$con = connection();
$con->set_charset("utf8mb4");  // Set charset to UTF-8

// Obtener los valores de los campos, eliminar espacios pero mantener acentos
$tipo = trim($_GET['tipo']);
$entrada = trim($_GET['entrada']);
$capacidad = trim($_GET['capacidad']);  // This is now the concatenated value (e.g., "500 GB")

// Preparar la consulta para evitar SQL Injection
$sql = "INSERT INTO discos (tipo, entrada, capacidad) VALUES (?, ?, ?)";
$stmt = $con->prepare($sql);

if ($stmt) {
    // Vincular parámetros
    $stmt->bind_param("sss", $tipo, $entrada, $capacidad);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        // Si la consulta se ejecuta correctamente, mostrar una alerta de éxito
        echo '<script type="text/javascript">
                alert("Disco agregado correctamente");
                window.location.href = "discos.php";
              </script>';

//Ejecuta la funcion registerAction que se encuentra en el archivo ValidaSesion
$id_usuario = $_SESSION['id'];
$accion = "Creación"; // Reemplaza con el tipo de acción (Creación, Actualización, Eliminación, Consulta, etc.)
$descripcion = "Creación realizada en la página " . $currentPage;
registerAction($id_usuario, $accion, $descripcion);

              
    } else {
        // Si hay un error al ejecutar la consulta, mostrar una alerta de error
        echo '<script type="text/javascript">
                alert("Error en la transacción: ' . addslashes($stmt->error) . '");
                window.location.href = "discos.php";
              </script>';
    }

    // Cerrar el statement
    $stmt->close();
} else {
    // Si hay un error al preparar la consulta, mostrar una alerta de error
    echo '<script type="text/javascript">
            alert("Error en la preparación de la consulta: ' . addslashes($con->error) . '");
            window.location.href = "discos.php";
          </script>';
}

// Cerrar la conexión
$con->close();
?>