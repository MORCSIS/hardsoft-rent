<?php
// Vista 1
// 1.- Muestra la info del cliente que devuelve seleccionado en vista anterior
// 2.- Selecciona el almacén receptor
include 'connection.php';
require_once 'ValidaSesion.php';
$con = connection();
$con->set_charset("utf8");

$id_clienteOrig = $_GET['id_clienteOrig'];

// Consulta para obtener detalles del almacén origen 
$sqlclienteO = "SELECT DISTINCT * FROM clientes WHERE id_cliente = $id_clienteOrig";
$result1 = $con->query($sqlclienteO);

// Consulta para obtener detalles del cliente receptor
$sqlclienteD = "SELECT DISTINCT * FROM clientes where NOT id_cliente in (1,100, 101)"; 
$result2 = $con->query($sqlclienteD);
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

            <!-- Tabla que trae info de origen seleecionado en paso anterior -->
            <div class="table-container">
            <H3>Detalle del almacén que entrega</H3>
    <table> 

        <thead>
            <tr>
            <th>ID Cliente</th>
            <th>ID Empresa</th>
            <th>Nombre del cliente</th>
            <th>RFC del cliente</th>
            <th>CP del cliente</th>
            </tr>
        </thead>
        <tbody>
        <?php
            if ($result1->num_rows > 0) {
                while ($row1 = $result1->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row1["id_cliente"] . "</td>";
                    echo "<td>" . $row1["id_empresa"] . "</td>";
                    echo "<td>" . $row1["nombre_cliente"] . "</td>";
                    echo "<td>" . $row1["cliente_rfc"] . "</td>";
                    echo "<td>" . $row1["cliente_cp"] . "</td>";
                    echo "</tr>";

                }} else {
                    echo "<tr><td colspan='14'>No se encontraron resultados</td></tr>";
            }
            ?>
            </tbody>
    </table>
    </div>

    <!-- Formulario para seleccionar cliente que devuelvue -->
        <div class="form-container">
        <h3>Paso 1: Seleccionar cliente destino</h3>
        <form action="salidastep2.php" method="GET">
        <input type="hidden" name="id_clienteOrig" value="<?php echo htmlspecialchars($id_clienteOrig); ?>">
        <label for="cliente">Cliente que recibe:</label>
        <select name="id_clienteDest" id="id_clienteDest" required>
            <option value="" autofocus>Seleccione una opción</option>
            <?php
            if ($result2->num_rows > 0) {
                while ($row = $result2->fetch_assoc()) {
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



