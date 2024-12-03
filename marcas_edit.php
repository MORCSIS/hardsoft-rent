<?php 
include 'connection.php';
require_once 'ValidaSesion.php';

$con = connection();
$con->set_charset("utf8");


// Verificar si se han recibido los parámetros necesarios
if(isset($_GET['id_marca']) && isset($_GET['nom_marca'])) {
    $id_marca = $_GET['id_marca'];
    $nom_marca = $_GET['nom_marca'];

    // Preparar la consulta SQL para actualizar el registro
    $sql = "UPDATE marcas SET nom_marca = ? WHERE id_marca = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("si", $nom_marca, $id_marca);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        echo "<script>
            alert('Artículo " . $id_marca . " - " . $nom_marca . " actualizado correctamente');
            window.location.href = 'marcas.php';
        </script>";
    } else {
        echo "<script>
            alert('Error al actualizar el marca: " . $stmt->error . "');
            window.location.href = 'marcas.php';
        </script>";
    }

    $stmt->close();
} else {
    // Si no se recibieron los parámetros, mostrar el formulario de edición
    $id_marca = $_GET['id_marca'] ?? '';
    $sql = "SELECT DISTINCT * FROM marcas WHERE id_marca = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id_marca);
    $stmt->execute();
    $result = $stmt->get_result();
    $marca = $result->fetch_assoc();
    $stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogos: Editar marca</title>
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

<form action="marcas_ValidUpt.php" method="POST">

ID marca <input type="text" name="id_marca" value="<?= htmlspecialchars($marca['id_marca'] ?? ''); ?>" readonly>
Nombre del marca <input type="text" name="nom_marca" value="<?= htmlspecialchars($marca['nom_marca'] ?? ''); ?>" autofocus required>

    <div class="button-container"> 
        <input type="submit" value="Guardar">
        <input type="button" value="Cancelar" onClick="location.href='marcas.php'">
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