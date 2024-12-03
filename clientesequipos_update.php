<?php
include 'connection.php';
require_once 'ValidaSesion.php';
$con = connection();
$con->set_charset("utf8");

// Obtener los valores de los parámetros
$id_clienteOrig = $_POST['id_clienteOrig'];
$id_clienteDest = $_POST['id_clienteDest'];
$id_estatus = $_POST['id_transaccion'];

// Este es un JSON con los IDs de los artículos seleccionados
$articulos_json = $_POST['articulos']; 

// Decodificar el JSON para obtener los IDs de los artículos
$ids_clientesequipos = json_decode($articulos_json, true);

// Convertir el array de IDs a un formato adecuado para la consulta SQL
$ids_clientesequipos_str = implode(',', array_map('intval', $ids_clientesequipos));

// Preparar la consulta SQL para actualizar la tabla clientesequipos


$sql = "UPDATE clientesequipos SET id_cliente = $id_clienteDest, id_estatus = $id_estatus 
WHERE codigodebarras IN ($ids_clientesequipos_str)";
$stmt = $con->prepare($sql);

if (empty($ids_clientesequipos_str)) {
    echo "<script>
    alert('Error, verifique todos los datos ingresados');
    window.location='" . $_SERVER['HTTP_REFERER'] . "';
    </script>";
    // Redirige a la página anterior con mensaje de error

    exit(); // Asegúrate de detener la ejecución del script después de redirigir
}


// Vincular parámetros
$stmt->bind_param('ii', $id_clienteDest, $id_estatus);

// Ejecutar la consulta
$stmt->execute();

    // Preparar la consulta SQL para actualizar la tabla materiales
    $sql_materiales = "UPDATE materiales SET id_estatus = $id_estatus 
    WHERE codigodebarras IN ($ids_clientesequipos_str)";
    $stmt_materiales = $con->prepare($sql_materiales);

    // Vincular parámetros
    $stmt_materiales->bind_param('i', $id_estatus);

    // Ejecutar la consulta en la tabla materiales
    $stmt_materiales->execute();

// Consulta para obtener detalles del cliente origen 
$sqlclienteO = "SELECT DISTINCT *  FROM clientes CL where CL.id_cliente = $id_clienteOrig";
$result1 = $con->query($sqlclienteO);

// Consulta para obtener detalles del cliente destino
$sqlclienteD = "SELECT DISTINCT *  FROM clientes CL where CL.id_cliente = $id_clienteDest";
$result2 = $con->query($sqlclienteD);

// Consulta para obtener detalle de articulos actualizados
$sqlcdbfin = "SELECT DISTINCT M.id_material, M.codigodebarras, M.id_articulo, M.id_marca, M.id_modelo, M.id_serie, M.id_estatus, AR.nom_articulo, MA.nom_marca, MO.nom_modelo, SE.nom_serie, ES.nom_estatus FROM materiales M LEFT JOIN articulos AR ON M.id_articulo = AR.id_articulo LEFT JOIN marcas MA ON M.id_marca = MA.id_marca LEFT JOIN modelos MO ON M.id_modelo = MO.id_modelo LEFT JOIN series SE ON M.id_serie = SE.id_serie LEFT JOIN estatus ES ON M.id_estatus = ES.id_estatus 
WHERE M.codigodebarras IN ($ids_clientesequipos_str)";
$result3 = $con->query($sqlcdbfin);

// Consulta para obtener detalles del transacción realizada
$sqltrans = "SELECT DISTINCT * FROM transacciones where id_transaccion = $id_estatus";
$resultTrans = $con->query($sqltrans);
?>

<!--SECCION DE REPORTE FINAL-->


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
<?php
    while ($rowT = $resultTrans->fetch_assoc()) {
    echo "<h2>" . $rowT["id_transaccion"] . " - " . $rowT["nombre_transaccion"] . "</h2>";}
    ?>
<section class="content-grid">

<!-- MOSTRAR TABLA CON CLIENTE SELECCIONADO -->
<div class="table-container">

<div class="button-container"> 
<div> <h3>Transacción finalizada exitosamente: </h3></div> <br><hr>
<div> <h3>    
    <?php
        // Establecer la zona horaria (ejemplo: Ciudad de México)
        date_default_timezone_set('America/Mexico_City');
        
        // Obtener la fecha y hora actual
        echo date('Y-m-d H:i:s');
        ?></h3>
</div><br><hr>
<input type="button" value="Imprimir" onclick="imprimirPantalla()">
</div> 

<H3>Detalle del Orígen</H3>

<script>
    // Función que invoca la ventana de impresión del navegador
    function imprimirPantalla() {
        window.print();
    }
</script>

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

    <H3>Detalle del Destino</H3>
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

    <table>
        <thead>
            <tr>
    <th>id material</th>
    <th>Código de Barras</th>
    <th>id articulo</th>
    <th>nombre articulo</th>
    <th>id marca</th>
    <th>nombre marca</th>
    <th>id modelo</th>
    <th>nombre modelo</th>
    <th>id serie</th>
    <th>nombre serie</th>
    <th>id estatus</th>
    <th>nombre estatus</th>
        </tr>
    </thead>
        <tbody>
        <?php
            if ($result3->num_rows > 0) {
                while ($row3 = $result3->fetch_assoc()) {
                    echo "<tr>";
    echo "<td>" . $row3["id_material"] . "</td>";
    echo "<td>" . $row3["codigodebarras"] . "</td>";
    echo "<td>" . $row3["id_articulo"] . "</td>";
    echo "<td>" . $row3["nom_articulo"] . "</td>";
    echo "<td>" . $row3["id_marca"] . "</td>";
    echo "<td>" . $row3["nom_marca"] . "</td>";
    echo "<td>" . $row3["id_modelo"] . "</td>";
    echo "<td>" . $row3["nom_modelo"] . "</td>";
    echo "<td>" . $row3["id_serie"] . "</td>";
    echo "<td>" . $row3["nom_serie"] . "</td>";
    echo "<td>" . $row3["id_estatus"] . "</td>";
    echo "<td>" . $row3["nom_estatus"] . "</td>";
    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='14'>No se encontraron resultados</td></tr>";
            }
                ?>
            </tbody>
    </table>

