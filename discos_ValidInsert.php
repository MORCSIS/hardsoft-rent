<?php

include('connection.php');
require_once 'ValidaSesion.php';
$con = connection();
$con->set_charset("utf8mb4");  // Set charset to UTF-8

// Obtener los valores de los campos y mantener acentos
$tipo = trim($_POST['tipo']);
$entrada = trim($_POST['entrada']);
$capacidad_numero = trim($_POST['capacidad']);
$unidad_capacidad = trim($_POST['unidad_capacidad']);

// Concatenar capacidad y unidad con un espacio en medio
$capacidad = $capacidad_numero . ' ' . $unidad_capacidad;

// Verificar si existe un disco con características similares
$sql_verificacion = "SELECT * FROM discos WHERE LOWER(tipo) LIKE LOWER(?) AND LOWER(entrada) LIKE LOWER(?) AND LOWER(capacidad) LIKE LOWER(?)";
$stmt_verificacion = $con->prepare($sql_verificacion);
$tipo_like = "%{$tipo}%";
$entrada_like = "%{$entrada}%";
$capacidad_like = "%{$capacidad}%";
$stmt_verificacion->bind_param("sss", $tipo_like, $entrada_like, $capacidad_like);
$stmt_verificacion->execute();
$resultado = $stmt_verificacion->get_result();

// Si existe algún disco parecido, se muestra un mensaje de confirmación
if ($resultado->num_rows > 0) {
    echo "<script>
        var confirmacion = confirm('Ya hay un registro parecido, desea continuar?');
        if (confirmacion) {
            window.location.href = 'discos_insert.php?tipo=' + encodeURIComponent('$tipo') + '&entrada=' + encodeURIComponent('$entrada') + '&capacidad=' + encodeURIComponent('$capacidad');
        } else {
            window.location.href = 'discos.php';
        }
    </script>";
} else {
    echo "<script>
        window.location.href = 'discos_insert.php?tipo=' + encodeURIComponent('$tipo') + '&entrada=' + encodeURIComponent('$entrada') + '&capacidad=' + encodeURIComponent('$capacidad');
    </script>";
}

$stmt_verificacion->close();
$con->close();
?>