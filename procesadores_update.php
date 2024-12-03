<?php 
include 'connection.php';
require_once 'ValidaSesion.php';

$con = connection();
$con->set_charset("utf8");

$id_procesador = $_GET['id_procesador'];
$nom_procesador = $_GET['nom_procesador'];
$generacion = $_GET['generacion'];
$velocidad = $_GET['velocidad'];
$sql = "SELECT DISTINCT * FROM procesadores WHERE id_procesador = '$id_procesador'";
$query = $con->query($sql);
$procesador = $query->fetch_assoc();  // Obtenemos los datos de la fila
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogos: Editar procesador</title>
    <link rel="stylesheet" href="styles.css">

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
Nombre del procesador <input type="text" name="nom_procesador" value="<?= htmlspecialchars($procesador['nom_procesador'] ?? ''); ?>" autofocus required>
Generación <input type="text" name="generacion" value="<?= htmlspecialchars($procesador['generacion'] ?? ''); ?>"  required>
Velocidad <input type="text" name="velocidad" value="<?= htmlspecialchars($procesador['velocidad'] ?? ''); ?>"  required>

    <div class="button-container"> 
        <input type="submit" value="Guardar">
        <input type="button" value="Cancelar" onClick="location.href='procesadores.php'">
    </div>
</form></div>
</section>
</div>
</body>
</html>
