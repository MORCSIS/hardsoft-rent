<?php
include 'connection.php';
require_once 'ValidaSesion.php';
// Formato de fecha actual
$con = connection();
$con->set_charset("utf8");

$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
$filename = 'equipos_' . date('YmdHis') . '.xls';

// Obtener los registros para la tabla actual de Articulos
$sql = "SELECT DISTINCT M.id_material, M.codigodebarras, M.id_articulo, M.id_marca, M.id_modelo, M.id_serie, M.id_estatus, AR.nom_articulo, MA.nom_marca, MO.nom_modelo, SE.nom_serie, ES.nom_estatus FROM materiales M LEFT JOIN articulos AR ON M.id_articulo = AR.id_articulo LEFT JOIN marcas MA ON M.id_marca = MA.id_marca LEFT JOIN modelos MO ON M.id_modelo = MO.id_modelo LEFT JOIN series SE ON M.id_serie = SE.id_serie LEFT JOIN estatus ES ON M.id_estatus = ES.id_estatus WHERE M.codigodebarras = '$busqueda' OR AR.nom_articulo LIKE '%$busqueda%' OR MA.nom_marca LIKE '%$busqueda%' OR MO.nom_modelo LIKE '%$busqueda%' OR SE.nom_serie LIKE '%$busqueda%' OR ES.nom_estatus LIKE '%$busqueda%'";
$result = $con->query($sql);

// Crear el archivo XLS
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");

echo "ID Material\tCódigo de Barras\tID Artículo\tNombre Artículo\tID Marca\tNombre Marca\tID Modelo\tNombre Modelo\tID Serie\tNombre Serie\tID Estatus\tNombre Estatus\n";

while ($row = $result->fetch_assoc()) {
    echo implode("\t", $row) . "\n";
}
exit;
?>
