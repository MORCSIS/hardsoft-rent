<?php 
include 'connection.php';
require_once 'ValidaSesion.php';
$con = connection();
$con->set_charset("utf8");

$id_articulo = $_GET['id_articulo'];
$sql = "SELECT DISTINCT * FROM articulos WHERE id_articulo = '$id_articulo'";
$query = $con->query($sql);
$articulo = $query->fetch_assoc();  // Obtenemos los datos de la fila
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
        <h2>Editar articulo</h2>
<section class="content-grid">
<div class="form-container">
<h3>Ingrese dato nuevo para la articulo</h3>

<form action="articulos_ValidUpt.php" method="POST">
<h3>Editar articulo</h3>

ID articulo <input type="text" name="id_articulo" value="<?= htmlspecialchars($articulo['id_articulo'] ?? ''); ?>" readonly>
Nombre del articulo <input type="text" name="nom_articulo" value="<?= htmlspecialchars($articulo['nom_articulo'] ?? ''); ?>" autofocus required>

    <div class="button-container"> 
        <input type="submit" value="Guardar">
        <input type="button" value="Cancelar" onClick="location.href='articulos.php'">
    </div>
</form></div>
</section>
</div>
</body>
</html>
