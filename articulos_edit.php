<?php 
include 'connection.php';
require_once 'ValidaSesion.php';

$con = connection();
$con->set_charset("utf8");


// Verificar si se han recibido los parámetros necesarios
if(isset($_GET['id_articulo']) && isset($_GET['nom_articulo'])) {
    $id_articulo = $_GET['id_articulo'];
    $nom_articulo = $_GET['nom_articulo'];

    // Preparar la consulta SQL para actualizar el registro
    $sql = "UPDATE articulos SET nom_articulo = ? WHERE id_articulo = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("si", $nom_articulo, $id_articulo);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        echo "<script>
            alert('Artículo " . $id_articulo . " - " . $nom_articulo . " actualizado correctamente');
            window.location.href = 'articulos.php';
        </script>";
    } else {
        echo "<script>
            alert('Error al actualizar el articulo: " . $stmt->error . "');
            window.location.href = 'articulos.php';
        </script>";
    }

    $stmt->close();
} else {
    // Si no se recibieron los parámetros, mostrar el formulario de edición
    $id_articulo = $_GET['id_articulo'] ?? '';
    $sql = "SELECT DISTINCT * FROM articulos WHERE id_articulo = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id_articulo);
    $stmt->execute();
    $result = $stmt->get_result();
    $articulo = $result->fetch_assoc();
    $stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogos: Editar articulo</title>
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

<form action="articulos_ValidUpt.php" method="POST">

ID articulo <input type="text" name="id_articulo" value="<?= htmlspecialchars($articulo['id_articulo'] ?? ''); ?>" readonly>
Nombre del articulo <input type="text" name="nom_articulo" value="<?= htmlspecialchars($articulo['nom_articulo'] ?? ''); ?>" autofocus required>

    <div class="button-container"> 
        <input type="submit" value="Guardar">
        <input type="button" value="Cancelar" onClick="location.href='articulos.php'">
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