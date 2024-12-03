<?php
include('connection.php');
require_once 'ValidaSesion.php';
$con = connection();
$con->set_charset("utf8");


// Obtener el valor del nombre del serie, eliminar espacios y convertir a mayúsculas
$nom_serie = mb_strtoupper(trim($_GET['nom_serie']));

// Preparar la consulta para evitar SQL Injection
$sql = "INSERT INTO series (nom_serie) VALUES (?)";
$stmt = $con->prepare($sql);

if ($stmt) {
    // Vincular parámetros, "s" significa que es una cadena (string)
    $stmt->bind_param("s", $nom_serie);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        // Si la consulta se ejecuta correctamente, mostrar una alerta de éxito
        echo '<script type="text/javascript">
                alert("Registro: ' . $nom_serie . ' agregado correctamente");
                window.location.href = "series.php";
              </script>';

//Ejecuta la funcion registerAction que se encuentra en el archivo ValidaSesion
$id_usuario = $_SESSION['id'];
$accion = "Creación"; // Reemplaza con el tipo de acción (Creación, Actualización, Eliminación, Consulta, etc.)
$descripcion = "Creación realizada en la página " . $currentPage;
registerAction($id_usuario, $accion, $descripcion);

    } else {
        // Si hay un error al ejecutar la consulta, mostrar una alerta de error
        echo '<script type="text/javascript">
                alert("Error en la transacción: Registro duplicado  '. $nom_serie .' ya existe, elija otro ");
                window.location.href = "series.php";
              </script>';
    }

    // Cerrar el statement
    $stmt->close();
} else {
    // Si hay un error al preparar la consulta, mostrar una alerta de error
    echo '<script type="text/javascript">
            alert("Error en la preparación de la consulta: ' . addslashes($con->error) . '");
            window.location.href = "series.php";
          </script>';
}

// Cerrar la conexión
$con->close();
?>
