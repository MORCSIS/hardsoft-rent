<?php
include('connection.php');
require_once 'ValidaSesion.php';
$con = connection();
$con->set_charset("utf8");


// Obtener el valor del nombre del articulo, eliminar espacios y convertir a mayúsculas
$nom_articulo = mb_strtoupper(trim($_GET['nom_articulo']));

// Preparar la consulta para evitar SQL Injection
$sql = "INSERT INTO articulos (nom_articulo) VALUES (?)";
$stmt = $con->prepare($sql);

if ($stmt) {
    // Vincular parámetros, "s" significa que es una cadena (string)
    $stmt->bind_param("s", $nom_articulo);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        // Si la consulta se ejecuta correctamente, mostrar una alerta de éxito
        echo '<script type="text/javascript">
                alert("Registro: ' . $nom_articulo . ' agregado correctamente");
                window.location.href = "articulos.php";
              </script>';
    //Ejecuta la funcion registerAction que se encuentra en el archivo ValidaSesion
    $id_usuario = $_SESSION['id'];
    $accion = "Creación"; // Reemplaza con el tipo de acción (Creación, Actualización, Eliminación, Consulta, etc.)
    $descripcion = "Creación realizada en la página " . $currentPage;
    registerAction($id_usuario, $accion, $descripcion);
              

            } else {
        // Si hay un error al ejecutar la consulta, mostrar una alerta de error
        echo '<script type="text/javascript">
                alert("Error en la transacción: Registro duplicado  '. $nom_articulo .' ya existe, elija otro ");
                window.location.href = "articulos.php";
              </script>';
    }

    // Cerrar el statement
    $stmt->close();
} else {
    // Si hay un error al preparar la consulta, mostrar una alerta de error
    echo '<script type="text/javascript">
            alert("Error en la preparación de la consulta: ' . addslashes($con->error) . '");
            window.location.href = "articulos.php";
          </script>';
}

// Cerrar la conexión
$con->close();
?>
