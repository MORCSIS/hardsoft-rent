<?php
include('connection.php');
require_once 'ValidaSesion.php';
$con = connection();
$con->set_charset("utf8");


// Obtener el nombre del modelo y convertirlo a mayúsculas
$nom_modelo = mb_strtoupper(trim($_POST['nom_modelo']));

// Verificar si existe un modelo con un nombre similar
$sql_verificacion = "SELECT * FROM modelos WHERE nom_modelo LIKE ?";
$stmt_verificacion = $con->prepare($sql_verificacion);
$nom_modelo_like = "%{$nom_modelo}%";
$stmt_verificacion->bind_param("s", $nom_modelo_like);
$stmt_verificacion->execute();
$resultado = $stmt_verificacion->get_result();

// Si existe algún modelo parecido, se muestra un mensaje de confirmación
if ($resultado->num_rows > 0) {
    echo "<script>
        var confirmacion = confirm('Ya hay un registro parecido, desea continuar?');
        if (confirmacion) {
            window.location.href = 'modelos_insert.php?nom_modelo={$nom_modelo}';
        } else {
            window.location.href = 'modelos.php';
        }
    </script>";
} else{
    echo "<script>
        window.location.href = 'modelos_insert.php?nom_modelo={$nom_modelo}';
        </script>";
    };

$stmt_verificacion->close();
$con->close();
?>