<?php 
include 'connection.php';
require_once 'ValidaSesion.php';
$con = connection();
$con->set_charset("utf8");

$id_marca = $_GET['id_marca'];
$sql = "SELECT DISTINCT * FROM marcas WHERE id_marca = '$id_marca'";
$query = $con->query($sql);
$marca = $query->fetch_assoc();  // Obtenemos los datos de la fila
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogos: Editar artículo</title>
    <link rel="stylesheet" href="styles.css">

</head>
<body>
    <?php include 'menu.php'; ?>
    <script src="menujs.js"></script>

    <div class="main-content" id="main-content">
        <button class="openbtn" onclick="toggleMenu()">&#9776;  </button>
        <h2>Editar marca</h2>
<section class="content-grid">
<div class="form-container">
<h3>Ingrese dato nuevo para la marca</h3>

<form action="marcas_ValidUpt.php" method="POST">
<h3>Editar marca</h3>

ID marca <input type="text" name="id_marca" value="<?= htmlspecialchars($marca['id_marca'] ?? ''); ?>" readonly>
Nombre del marca <input type="text" name="nom_marca" value="<?= htmlspecialchars($marca['nom_marca'] ?? ''); ?>" autofocus required>

    <div class="button-container"> 
        <input type="submit" value="Guardar">
        <input type="button" value="Cancelar" onClick="location.href='marcas.php'">
    </div>
</form></div>
</section>
</div>
</body>
</html>
