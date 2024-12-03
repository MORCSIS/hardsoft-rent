<?php
// En esta primera vista:
// 1.- Selecciona cliente que devuelve

include 'connection.php';
require_once 'ValidaSesion.php';
$con = connection();
$con->set_charset("utf8");

// Consulta para obtener cliente Orígen
$sql = "SELECT DISTINCT CL.id_cliente, CL.nombre_cliente, CE.id_estatus FROM clientes CL LEFT JOIN clientesequipos CE ON CL.id_cliente
where CL.id_cliente in (1,100, 101) AND CE.id_estatus = 1"; 
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

<h2>Salidas de almacén</h2>
<section class="content-grid">

<div class="form-container">  
<h3>Paso 0: Seleccionar almacén origen</h3>
<form action="salidastep1.php" method="GET">
        <label for="cliente">Almacén origen:</label>
        <select name="id_clienteOrig" id="id_clienteOrig" required>
            <option value="" autofocus>Seleccione una opción</option>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['id_cliente'] . "'>" . $row['id_cliente'] . " - " . $row['nombre_cliente'] . "</option>";
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