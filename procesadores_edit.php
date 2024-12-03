<?php 
include 'connection.php';
require_once 'ValidaSesion.php';
$con = connection();
$con->set_charset("utf8");


// Verificar si se han recibido los parámetros necesarios
if(isset($_GET['id_procesador']) && isset($_GET['nom_procesador'])
&& isset($_GET['generacion'])&& isset($_GET['velocidad']))
 {
    $id_procesador = $_GET['id_procesador'];
    $nom_procesador = $_GET['nom_procesador'];
    $generacion = $_GET['generacion'];
    $velocidad = $_GET['velocidad'];

    // Preparar la consulta SQL para actualizar el registro
    $sql = "UPDATE procesadores SET nom_procesador = ?, generacion = ?, velocidad = ?      
    WHERE id_procesador = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("sssi", $nom_procesador, $generacion, $velocidad, $id_procesador);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        echo "<script>
            alert('procesador " . $id_procesador . " - " . $nom_procesador . " actualizado correctamente');
            window.location.href = 'procesadores.php';
        </script>";
    } else {
        echo "<script>
            alert('Error al actualizar el procesador: " . $stmt->error . "');
            window.location.href = 'procesadores.php';
        </script>";
    }

    $stmt->close();
} else {
    // Si no se recibieron los parámetros, mostrar el formulario de edición
    $id_procesador = $_GET['id_procesador'] ?? '';
    $sql = "SELECT DISTINCT * FROM procesadores WHERE id_procesador = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id_procesador);
    $stmt->execute();
    $result = $stmt->get_result();
    $procesador = $result->fetch_assoc();
    $stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogos: Editar procesador</title>
    <link rel="stylesheet" href="styles.css">
    <script src="Funciones.js"></script>   
</head>
<body>
    <?php include 'menu.php'; ?>
    <script src="menujs.js"></script>

<div class="main-content" id="main-content">
<button class="openbtn" onclick="toggleMenu()">&#9776;  </button>
<section class="content-grid">
<div class="form-container">

<form action="procesadores_ValidUpt.php" method="POST">
<h3>Editar procesador</h3>

ID procesador <input type="text" name="id_procesador" value="<?= htmlspecialchars($procesador['id_procesador'] ?? ''); ?>" readonly>
Nombre de procesador <input type="text" name="nom_procesador" value="<?= htmlspecialchars($procesador['nom_procesador'] ?? ''); ?>" autofocus required>
Generación <input type="text" name="generacion" value="<?= htmlspecialchars($procesador['generacion'] ?? ''); ?>"  required>
Velocidad <input type="text" name="velocidad" value="<?= htmlspecialchars($procesador['velocidad'] ?? ''); ?>"  required>
    <div class="button-container"> 
        <input type="submit" value="Guardar">
        <input type="button" value="Cancelar" onClick="location.href='procesadores.php'">
    </div>
</form>
</div>
</section>
</div>
</body>
</html>
<?php
}
$con->close();
?>