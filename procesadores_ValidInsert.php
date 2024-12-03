<?php
include('connection.php');
require_once 'ValidaSesion.php';

$con = connection();
$con->set_charset("utf8");


// Obtener el nombre del procesador y convertirlo a mayúsculas
$nom_procesador = mb_strtoupper(trim($_POST['nom_procesador']));
$generacion = $_POST['generacion'];
$velocidad = $_POST['velocidad'];

// Verificar si existe un procesador con un nombre similar
$sql_verificacion = "SELECT * FROM procesadores WHERE nom_procesador LIKE ?";
$stmt_verificacion = $con->prepare($sql_verificacion);
$nom_procesador_like = "%{$nom_procesador}%";
$stmt_verificacion->bind_param("s", $nom_procesador_like);
$stmt_verificacion->execute();
$resultado = $stmt_verificacion->get_result();

// Si existe algún procesador parecido, se muestra un mensaje de confirmación
if ($resultado->num_rows > 0) {
    echo "<script>
        var confirmacion = confirm('Ya hay un registro parecido, desea continuar?');
        if (confirmacion) {
        window.location.href = 'procesadores_insert.php?nom_procesador=' + encodeURIComponent('$nom_procesador') + '&generacion=' + encodeURIComponent('$generacion') + '&velocidad=' + encodeURIComponent('$velocidad');
        } else {
            window.location.href = 'procesadores.php';
        }
    </script>";
} else{
    echo "<script>
        window.location.href = 'procesadores_insert.php?nom_procesador=' + encodeURIComponent('$nom_procesador') + '&generacion=' + encodeURIComponent('$generacion') + '&velocidad=' + encodeURIComponent('$velocidad');
        </script>";
    };

$stmt_verificacion->close();
$con->close();
?>