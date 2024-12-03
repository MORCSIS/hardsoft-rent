<?php
// En esta primera vista:
// 1.- Selecciona cliente que devuelve

include 'connection.php';
require_once 'ValidaSesion.php';
$con = connection();
$con->set_charset("utf8");

// Consulta para obtener cliente Orígen
$sql = "SELECT DISTINCT CE.id_cliente, CL.nombre_cliente 
FROM clientesequipos CE LEFT JOIN clientes CL ON CE.id_cliente = CL.id_cliente
where CE.id_cliente > 101"; 
$result = $con->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="Funciones.js" defer></script>
    <title>Inventarios HardSoft Rent</title>
</head>

<body>
    <?php include 'menu.php'; ?>
    <script src="menujs.js"></script>
<div class="main-content" id="main-content">
<button class="openbtn" onclick="toggleMenu()">&#9776;  </button>

<h2>Devoluciones</h2>
<section class="content-grid">

<div class="form-container">  
<h3>Paso 0: Seleccionar cliente origen</h3>
<form action="entradastep1.php" method="GET">
        <label for="cliente">Cliente origen:</label>
        <select name="id_clienteOrig" id="id_clienteOrig" required>
            <option value="" autofocus>Seleccione una opción</option>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Codificar los datos del cliente
                    $nombre_cliente = htmlspecialchars($row['nombre_cliente'], ENT_QUOTES, 'UTF-8');
                    $id_cliente = htmlspecialchars($row['id_cliente'], ENT_QUOTES, 'UTF-8');
                    
                    // Mostrar la opción en el select
                    echo "<option value='" . $id_cliente . "'>" . $id_cliente . " - " . $nombre_cliente . "</option>";
                }
            }
            ?>
        </select>
<div class="button-container">  
        <input type="submit" value=" Siguiente"> 
        <input type="button" value=" Cancelar" onClick="location.href=' Inicio.php'">    
</div>
</form>
</div>
</section>
</div>
</body>
</html>