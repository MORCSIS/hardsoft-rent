<?php
include('connection.php');
require_once 'ValidaSesion.php';
$con = connection();
$con->set_charset("utf8");


// Obtener el nombre del articulo y convertirlo a mayúsculas
$id_articulo = isset($_POST['id_articulo']) ? $_POST['id_articulo'] : '';
$nom_articulo = mb_strtoupper(trim($_POST['nom_articulo'] ?? ''));

// Verificar si existe un articulo con un nombre similar
$sql_verificacion = "SELECT * FROM articulos WHERE nom_articulo LIKE ?";
$stmt_verificacion = $con->prepare($sql_verificacion);
$nom_articulo_like = "%{$nom_articulo}%";
$stmt_verificacion->bind_param("s", $nom_articulo_like);
$stmt_verificacion->execute();
$resultado = $stmt_verificacion->get_result();

// Codificar los parámetros para la URL
$id_articulo_encoded = urlencode($id_articulo);
$nom_articulo_encoded = urlencode($nom_articulo);

// Si existe algún articulo parecido, se muestra un mensaje de confirmación
if ($resultado->num_rows > 0) {
    echo "<script>
        var confirmacion = confirm('Ya hay un registro parecido, desea continuar?');
        if (confirmacion) {
            window.location.href = 'articulos_edit.php?id_articulo=$id_articulo_encoded&nom_articulo=$nom_articulo_encoded';
        } else {
            window.location.href = 'articulos.php';
        }
    </script>";
} else {
    // Si no hay articulos parecidos, redirigir directamente
    echo "<script>
            window.location.href = 'articulos_edit.php?id_articulo=$id_articulo_encoded&nom_articulo=$nom_articulo_encoded';
    </script>";
}

$stmt_verificacion->close();
$con->close();
?>