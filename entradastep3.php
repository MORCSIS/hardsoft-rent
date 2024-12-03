<?php
include 'connection.php';
require_once 'ValidaSesion.php';
$con = connection();
$con->set_charset("utf8");

$id_clienteOrig = $_GET['id_clienteOrig'];
$id_clienteDest = $_GET['id_clienteDest'];
$id_transaccion = $_GET['id_transaccion'];

// Consulta para lista de código de barras del cliente que devuelve
$sqlcb = "SELECT DISTINCT codigodebarras FROM clientesequipos
WHERE id_cliente = $id_clienteOrig";
$resultadocb = $con->query($sqlcb);

// Consulta para obtener detalles del cliente origen 
$sqlclienteO = "SELECT DISTINCT *  FROM clientes CL where CL.id_cliente = $id_clienteOrig";
$result1 = $con->query($sqlclienteO);

// Consulta para obtener detalles del cliente destino
$sqlclienteD = "SELECT DISTINCT *  FROM clientes CL where CL.id_cliente = $id_clienteDest";
$result2 = $con->query($sqlclienteD);

// Consulta para obtener las transacciones disponibles
$sqltransac = "SELECT DISTINCT *  FROM transacciones TR where TR.id_transaccion = 1";
$result3 = $con->query($sqltransac);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Inventarios HardSoft Rent</title>

</head>

<body>
    <?php include 'menu.php'; ?>
    <script src="menujs.js"></script>


<div class="main-content" id="main-content">
<button class="openbtn" onclick="toggleMenu()">&#9776;  </button>

<h2>Devoluciones</h2>
<section class="content-grid">
<!-- Esta tabla muestra el detalle del CLIENTE QUE DEVUELVE -->

<div class="table-container">
            <H3>Detalle del cliente que devuelve</H3>

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
<!-- Esta tabla muestra el detalle del ALMACÉN QUE RECIBE -->

    <H3>Detalle del almacén que recibe</H3>
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
            if ($result2->num_rows > 0) {
                while ($row2 = $result2->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row2["id_cliente"] . "</td>";
                    echo "<td>" . $row2["id_empresa"] . "</td>";
                    echo "<td>" . $row2["nombre_cliente"] . "</td>";
                    echo "<td>" . $row2["cliente_rfc"] . "</td>";
                    echo "<td>" . $row2["cliente_cp"] . "</td>";
                    echo "</tr>";

                }} else {
                    echo "<tr><td colspan='14'>No se encontraron resultados</td></tr>";
            }
            ?>
            </tbody>
    </table>

<!-- Esta tabla muestra el detalle de la TRANSACCIÓN seleccionada -->
<h3>Transacción seleccionada</h3>
<table> 
    <thead>
        <tr>
            <th>ID Transacción</th>
            <th>Nombre Transacción</th>
        </tr>
    </thead>
    <tbody>
        <?php
if ($result3->num_rows > 0) {
    while ($row3 = $result3->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row3["id_transaccion"] . "</td>";
        echo "<td>" . $row3["nombre_transaccion"] . "</td>";
        echo "</tr>";
    }} else {
        echo "<tr><td colspan='14'>No se encontraron resultados</td></tr>";
    }
    ?>
</tbody>
</table>

<form action="clientesequipos_update.php" method="POST" id="guardar_form">
<input type="hidden" name="id_clienteOrig" value="<?php echo htmlspecialchars($id_clienteOrig); ?>">
<input type="hidden" name="id_clienteDest" value="<?php echo htmlspecialchars($id_clienteDest); ?>">
<input type="hidden" name="id_transaccion" value="<?php echo htmlspecialchars($id_transaccion); ?>">
<input type="hidden" name="articulos" id="articulos">

  <div class="button-container">

<input type="submit" value="Guardar">
<input type="button" value=" Cancelar" onClick="location.href=' Inicio.php'">    
</div>
</form>

</div>

<div class="form-container">
<form id="articulos_form">
        <h3>Paso 3: Seleccionar articulos</h3>
        <div class="button-container">

            <label for="articulo">Artículo:</label>
            <select id="articulo" name="codigodebarras" autofocus>
                <option value="" >Seleccione un artículo </option>
                <?php
            if ($resultadocb->num_rows > 0) {
                while ($rowCB = $resultadocb->fetch_assoc()) {
                    echo "<option value='" . $rowCB['codigodebarras'] . "'>" . $rowCB['codigodebarras'] . "</option>";
                }
            }
            ?>
        </select>
        <input type="button" value="Agregar" onclick="agregarArticulo()">
    </div>
    </form>

<h3>Artículos Seleccionados</h3>
<table id="tabla_articulos">
<thead>
<tr><th>ID Artículo</th></tr>
</thead>
<tbody></tbody>
</table>
</div>

<script>
function agregarArticulo() {
var articuloSelect = document.getElementById('articulo');
var articuloId = articuloSelect.value;

if (articuloId === "") {
alert("Seleccione un artículo.");
return;
}

var articuloText = articuloSelect.options[articuloSelect.selectedIndex].text;
var tablaArticulos = document.getElementById('tabla_articulos').getElementsByTagName('tbody')[0];

// Verifica si el artículo ya ha sido agregado
var filas = tablaArticulos.getElementsByTagName('tr');
for (var i = 0; i < filas.length; i++) {
if (filas[i].getAttribute('data-id') === articuloId) {
alert("Este artículo ya ha sido agregado.");
return;
}
}

// Crear una nueva fila en la tabla
var fila = tablaArticulos.insertRow();
fila.setAttribute('data-id', articuloId);

var celdaId = fila.insertCell(0);
celdaId.textContent = articuloId;

var celdaAccion = fila.insertCell(1);
var btnEliminar = document.createElement('button');
btnEliminar.textContent = 'Eliminar';
btnEliminar.type = 'button';

// Establecer el color gris oscuro
btnEliminar.style.backgroundColor = '#A9A9A9'; // Color gris oscuro
btnEliminar.style.color = '#FFFFFF';           // Texto blanco
btnEliminar.style.border = 'none';             // Sin borde (opcional)
btnEliminar.style.padding = '2px 5px';        // Espaciado del botón (opcional)
btnEliminar.style.cursor = 'pointer';          // Cambiar cursor al pasar sobre el botón


btnEliminar.onclick = function () {
tablaArticulos.deleteRow(fila.rowIndex - 1);
actualizarListaArticulos();
// Establecer el enfoque en el campo de selección de artículos
document.getElementById('articulo').focus();
};
celdaAccion.appendChild(btnEliminar);

// Actualiza la lista de artículos seleccionados
actualizarListaArticulos();

// Establecer el enfoque en el campo de selección de artículos después de agregar
articuloSelect.focus();
}

function actualizarListaArticulos() {
var tablaArticulos = document.getElementById('tabla_articulos').getElementsByTagName('tbody')[0];
var filas = tablaArticulos.getElementsByTagName('tr');
var articulos = [];

for (var i = 0; i < filas.length; i++) {
articulos.push(filas[i].getAttribute('data-id'));
}

document.getElementById('articulos').value = JSON.stringify(articulos);
}
</script>
</section>
</div>
</body>
</html>