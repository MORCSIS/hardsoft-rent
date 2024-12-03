<?php 
include 'connection.php';
require_once 'ValidaSesion.php';
$con = connection();
$con->set_charset("utf8");

$id_serie = $_GET['id_serie'];
$sql = "SELECT DISTINCT * FROM series WHERE id_serie = '$id_serie'";
$query = $con->query($sql);
$serie = $query->fetch_assoc();  // Obtenemos los datos de la fila
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogos: Editar serie</title>
    <link rel="stylesheet" href="styles.css">

</head>
<body>
    <?php include 'menu.php'; ?>
    <script src="menujs.js"></script>

<div class="main-content" id="main-content">
<button class="openbtn" onclick="toggleMenu()">&#9776;  </button>
<section class="content-grid">
<div class="form-container">

<form action="series_ValidUpt.php" method="POST">
<h3>Editar serie</h3>

ID serie <input type="text" name="id_serie" value="<?= htmlspecialchars($serie['id_serie'] ?? ''); ?>" readonly>
Nombre del serie <input type="text" name="nom_serie" value="<?= htmlspecialchars($serie['nom_serie'] ?? ''); ?>" autofocus required>

    <div class="button-container"> 
        <input type="submit" value="Guardar">
        <input type="button" value="Cancelar" onClick="location.href='series.php'">
    </div>
</form></div>
</section>
</div>
</body>
</html>
