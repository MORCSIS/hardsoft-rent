<?php 
include 'connection.php';
require_once 'ValidaSesion.php';
$con = connection();
$con->set_charset("utf8");


// Verificar si se han recibido los parámetros necesarios
if(isset($_GET['id_modelo']) && isset($_GET['nom_modelo'])) {
    $id_modelo = $_GET['id_modelo'];
    $nom_modelo = $_GET['nom_modelo'];

    // Preparar la consulta SQL para actualizar el registro
    $sql = "UPDATE modelos SET nom_modelo = ? WHERE id_modelo = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("si", $nom_modelo, $id_modelo);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        echo "<script>
            alert('Artículo " . $id_modelo . " - " . $nom_modelo . " actualizado correctamente');
            window.location.href = 'modelos.php';
        </script>";
    } else {
        echo "<script>
            alert('Error al actualizar el modelo: " . $stmt->error . "');
            window.location.href = 'modelos.php';
        </script>";
    }

    $stmt->close();
} else {
    // Si no se recibieron los parámetros, mostrar el formulario de edición
    $id_modelo = $_GET['id_modelo'] ?? '';
    $sql = "SELECT DISTINCT * FROM modelos WHERE id_modelo = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id_modelo);
    $stmt->execute();
    $result = $stmt->get_result();
    $modelo = $result->fetch_assoc();
    $stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogos: Editar modelo</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'menu.php'; ?>
    <script src="menujs.js"></script>

<div class="main-content" id="main-content">
<button class="openbtn" onclick="toggleMenu()">&#9776;  </button>
<section class="content-grid">
<div class="form-container">

<form action="modelos_ValidUpt.php" method="POST">
<h3>Editar modelo</h3>

ID modelo <input type="text" name="id_modelo" value="<?= htmlspecialchars($modelo['id_modelo'] ?? ''); ?>" readonly>
Nombre del modelo <input type="text" name="nom_modelo" value="<?= htmlspecialchars($modelo['nom_modelo'] ?? ''); ?>" autofocus required>

    <div class="button-container"> 
        <input type="submit" value="Guardar">
        <input type="button" value="Cancelar" onClick="location.href='modelos.php'">
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