<?php
include('connection.php');
require_once 'ValidaSesion.php';
$con = connection();
$con->set_charset("utf8");


// Obtener el nombre del marca y convertirlo a mayúsculas
$id_marca = isset($_POST['id_marca']) ? $_POST['id_marca'] : '';
$nom_marca = mb_strtoupper(trim($_POST['nom_marca'] ?? ''));

// Verificar si existe un marca con un nombre similar
$sql_verificacion = "SELECT * FROM marcas WHERE nom_marca LIKE ?";
$stmt_verificacion = $con->prepare($sql_verificacion);
$nom_marca_like = "%{$nom_marca}%";
$stmt_verificacion->bind_param("s", $nom_marca_like);
$stmt_verificacion->execute();
$resultado = $stmt_verificacion->get_result();

// Codificar los parámetros para la URL
$id_marca_encoded = urlencode($id_marca);
$nom_marca_encoded = urlencode($nom_marca);

// Si existe algún marca parecido, se muestra un mensaje de confirmación
if ($resultado->num_rows > 0) {
    echo "<script>
        var confirmacion = confirm('Ya hay un registro parecido, desea continuar?');
        if (confirmacion) {
            window.location.href = 'marcas_edit.php?id_marca=$id_marca_encoded&nom_marca=$nom_marca_encoded';
        } else {
            window.location.href = 'marcas.php';
        }
    </script>";
} else {
    // Si no hay marcas parecidos, redirigir directamente
    echo "<script>
            window.location.href = 'marcas_edit.php?id_marca=$id_marca_encoded&nom_marca=$nom_marca_encoded';
    </script>";
}

$stmt_verificacion->close();
$con->close();
?>