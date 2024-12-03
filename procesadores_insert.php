<?php
 include('connection.php');
 require_once 'ValidaSesion.php';


$con = connection();
$con->set_charset("utf8");


// Obtener el valor del nombre del procesador, eliminar espacios y convertir a mayúsculas
$nom_procesador = mb_strtoupper(trim($_GET['nom_procesador']));
$generacion = $_GET['generacion'];
$velocidad = $_GET['velocidad'];

// Preparar la consulta para evitar SQL Injection
$sql = "INSERT INTO procesadores (nom_procesador, generacion, velocidad) VALUES (?, ?, ?)";
$stmt = $con->prepare($sql);

if ($stmt) {
    // Vincular parámetros, "s" significa que es una cadena (string)
    $stmt->bind_param("sss", $nom_procesador, $generacion, $velocidad);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        // Si la consulta se ejecuta correctamente, mostrar una alerta de éxito
        echo '<script type="text/javascript">
                alert("Registro: ' . $nom_procesador . ' agregado correctamente");
                window.location.href = "procesadores.php";
              </script>';

//Ejecuta la funcion registerAction que se encuentra en el archivo ValidaSesion
$id_usuario = $_SESSION['id'];
$accion = "Creación"; // Reemplaza con el tipo de acción (Creación, Actualización, Eliminación, Consulta, etc.)
$descripcion = "Creación realizada en la página " . $currentPage;
registerAction($id_usuario, $accion, $descripcion);

    } else {
        // Si hay un error al ejecutar la consulta, mostrar una alerta de error
        echo '<script type="text/javascript">
                alert("Error en la transacción: Registro duplicado  '. $nom_procesador .' ya existe, elija otro ");
                window.location.href = "procesadores.php";
              </script>';
    }

    // Cerrar el statement
    $stmt->close();
} else {
    // Si hay un error al preparar la consulta, mostrar una alerta de error
    echo '<script type="text/javascript">
            alert("Error en la preparación de la consulta: ' . addslashes($con->error) . '");
            window.location.href = "procesadores.php";
          </script>';
}

// Cerrar la conexión
$con->close();
?>
