<?php
include('connection.php');
require_once 'ValidaSesion.php';
$con = connection();

// Obtener el nombre del artículo y convertirlo a mayúsculas
$nom_articulo = mb_strtoupper($_POST['nom_articulo']);

// Verificar si existe un artículo con un nombre similar
$sql_verificacion = "SELECT * FROM articulos WHERE nom_articulo LIKE ?";
$stmt_verificacion = $con->prepare($sql_verificacion);
$nom_articulo_like = "%{$nom_articulo}%";
$stmt_verificacion->bind_param("s", $nom_articulo_like);
$stmt_verificacion->execute();
$resultado = $stmt_verificacion->get_result();

// Si existe algún artículo parecido, se muestra un mensaje de confirmación
if ($resultado->num_rows > 0) {
    echo "<script>
        var confirmacion = confirm('Ya hay un registro parecido, desea continuar?');
        if (confirmacion) {
            window.location.href = 'articulos_insert.php?nom_articulo={$nom_articulo}';
        } else {
            window.location.href = 'articulos.php';
        }
    </script>";
    exit();
}

$stmt_verificacion->close();
$con->close();
?>