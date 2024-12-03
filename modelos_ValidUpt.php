<?php
include('connection.php');
require_once 'ValidaSesion.php';
$con = connection();
$con->set_charset("utf8");


// Obtener el nombre del modelo y convertirlo a mayúsculas
$id_modelo = isset($_POST['id_modelo']) ? $_POST['id_modelo'] : '';
$nom_modelo = mb_strtoupper(trim($_POST['nom_modelo'] ?? ''));

// Verificar si existe un modelo con un nombre similar
$sql_verificacion = "SELECT * FROM modelos WHERE nom_modelo LIKE ?";
$stmt_verificacion = $con->prepare($sql_verificacion);
$nom_modelo_like = "%{$nom_modelo}%";
$stmt_verificacion->bind_param("s", $nom_modelo_like);
$stmt_verificacion->execute();
$resultado = $stmt_verificacion->get_result();

// Codificar los parámetros para la URL
$id_modelo_encoded = urlencode($id_modelo);
$nom_modelo_encoded = urlencode($nom_modelo);

// Si existe algún modelo parecido, se muestra un mensaje de confirmación
if ($resultado->num_rows > 0) {
    echo "<script>
        var confirmacion = confirm('Ya hay un registro parecido, desea continuar?');
        if (confirmacion) {
            window.location.href = 'modelos_edit.php?id_modelo=$id_modelo_encoded&nom_modelo=$nom_modelo_encoded';
        } else {
            window.location.href = 'modelos.php';
        }
    </script>";
} else {
    // Si no hay modelos parecidos, redirigir directamente
    echo "<script>
            window.location.href = 'modelos_edit.php?id_modelo=$id_modelo_encoded&nom_modelo=$nom_modelo_encoded';
    </script>";
}

$stmt_verificacion->close();
$con->close();
?>