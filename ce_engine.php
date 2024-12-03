<?php
include 'connection.php';
require_once 'ValidaSesion.php';
$con = connection();
$con->set_charset("utf8");

$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = isset($_GET['records_per_page']) ? (int)$_GET['records_per_page'] : 10;
$offset = ($page - 1) * $records_per_page;

// Contar el total de registros
$count_sql = "SELECT COUNT(DISTINCT CE.codigodebarras, CE.id_cliente,
CL.nombre_cliente, CL.cliente_rfc, CL.id_empresa, CL.cliente_cp) AS total
FROM clientesequipos CE
LEFT JOIN clientes CL ON CE.id_cliente = CL.id_cliente
WHERE CE.id_cliente = '$busqueda' OR CL.nombre_cliente LIKE '%$busqueda%'";
$count_result = $con->query($count_sql);
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);

// Obtener los registros para la tabla actual de Clientes
$sql0 = "SELECT DISTINCT CE.codigodebarras, CE.id_cliente, 
CL.nombre_cliente, CL.cliente_rfc, CL.id_empresa, CL.cliente_cp
FROM clientesequipos CE
LEFT JOIN clientes CL ON CE.id_cliente = CL.id_cliente
WHERE CE.id_cliente = '$busqueda' OR CL.nombre_cliente LIKE '%$busqueda%'
LIMIT 1";

// Obtener los registros para la tabla actual de Articulos
$sql = "SELECT DISTINCT CE.codigodebarras, CE.id_cliente,
CL.cliente_rfc, CL.nombre_cliente, MF.nom_articulo, MF.nom_marca,
MF.nom_modelo, MF.nom_serie, MF.nom_estatus
FROM clientesequipos CE
LEFT JOIN clientes CL ON CE.id_cliente = CL.id_cliente
LEFT JOIN(
SELECT DISTINCT M.codigodebarras, AR.nom_articulo, MA.nom_marca,
MO.nom_modelo, SE.nom_serie, ES.nom_estatus
    FROM materiales M
    LEFT JOIN articulos AR ON M.id_articulo = AR.id_articulo
    LEFT JOIN marcas MA ON M.id_marca = MA.id_marca
    LEFT JOIN modelos MO ON M.id_modelo = MO.id_modelo
    LEFT JOIN series SE ON M.id_serie = SE.id_serie
    LEFT JOIN estatus ES ON M.id_estatus = ES.id_estatus
    )MF ON CE.codigodebarras = MF.codigodebarras
WHERE CE.id_cliente = '$busqueda' OR CL.nombre_cliente LIKE '%$busqueda%'
LIMIT $offset, $records_per_page";
$result = $con->query($sql);
$result1 = $con->query($sql0);

//Ejecuta la funcion registerAction que se encuentra en el archivo ValidaSesion
$id_usuario = $_SESSION['id'];
$accion = "Consulta"; // Reemplaza con el tipo de acción (Creación, Actualización, Eliminación, Consulta, etc.)
$descripcion = "Consulta realizada en la página " . $currentPage;
registerAction($id_usuario, $accion, $descripcion);
?>