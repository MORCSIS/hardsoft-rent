<?php
include('connection.php');
require_once 'ValidaSesion.php';
$con = connection();
$con->set_charset("utf8");


// Obtener el nombre del serie y convertirlo a mayúsculas
$nom_serie = mb_strtoupper(trim($_POST['nom_serie']));

// Verificar si existe un serie con un nombre similar
$sql_verificacion = "SELECT * FROM series WHERE nom_serie LIKE ?";
$stmt_verificacion = $con->prepare($sql_verificacion);
$nom_serie_like = "%{$nom_serie}%";
$stmt_verificacion->bind_param("s", $nom_serie_like);
$stmt_verificacion->execute();
$resultado = $stmt_verificacion->get_result();

// Si existe algún serie parecido, se muestra un mensaje de confirmación
if ($resultado->num_rows > 0) {
    echo "<script>
        var confirmacion = confirm('Ya hay un registro parecido, desea continuar?');
        if (confirmacion) {
            window.location.href = 'series_insert.php?nom_serie={$nom_serie}';
        } else {
            window.location.href = 'series.php';
        }
    </script>";
} else{
    echo "<script>
        window.location.href = 'series_insert.php?nom_serie={$nom_serie}';
        </script>";
    };

$stmt_verificacion->close();
$con->close();
?>