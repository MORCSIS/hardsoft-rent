<?php
include('connection.php');
require_once 'ValidaSesion.php';

$con = connection();
$con->set_charset("utf8");


// Obtener el nombre del procesador y convertirlo a mayúsculas
$id_procesador = isset($_POST['id_procesador']) ? $_POST['id_procesador'] : '';
$nom_procesador = mb_strtoupper(trim($_POST['nom_procesador'] ?? ''));
$generacion = mb_strtoupper(trim($_POST['generacion'] ?? ''));
$velocidad = $_POST['velocidad'] ?? '';

// Verificar si existe un procesador con un nombre similar
$sql_verificacion = "SELECT * FROM procesadores WHERE nom_procesador LIKE ?";
$stmt_verificacion = $con->prepare($sql_verificacion);
$nom_procesador_like = "%{$nom_procesador}%";
$stmt_verificacion->bind_param("s", $nom_procesador_like);
$stmt_verificacion->execute();
$resultado = $stmt_verificacion->get_result();

// Codificar los parámetros para la URL
$id_procesador_encoded = urlencode($id_procesador);
$nom_procesador_encoded = urlencode($nom_procesador);
$generacion_encoded = urlencode($generacion);
$velocidad_encoded = urlencode($velocidad);

// Si existe algún procesador parecido, se muestra un mensaje de confirmación
if ($resultado->num_rows > 0) {
    echo "<script>
        var confirmacion = confirm('Ya hay un registro parecido, desea continuar?');
        if (confirmacion) {
            window.location.href = 'procesadores_edit.php?id_procesador=$id_procesador_encoded&nom_procesador=$nom_procesador_encoded&generacion=$generacion_encoded&velocidad=$velocidad_encoded';
        } else {
            window.location.href = 'procesadores.php';
        }
    </script>";
} else {
    // Si no hay procesadors parecidos, redirigir directamente
    echo "<script>
            window.location.href = 'procesadores_edit.php?id_procesador=$id_procesador_encoded&nom_procesador=$nom_procesador_encoded&generacion=$generacion_encoded&velocidad=$velocidad_encoded';
    </script>";
}

$stmt_verificacion->close();
$con->close();
?>