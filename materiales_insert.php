<?php
include('connection.php');
require_once 'ValidaSesion.php';
$con = connection();

$id_material = NULL;
$id_articulo = intval($_POST ['id_articulo']);
$id_marca = intval($_POST ['id_marca']);
$id_modelo = intval($_POST ['id_modelo']);
$id_serie = intval($_POST ['id_serie']);
$id_estatus = intval($_POST ['id_estatus']);
$id_procesador = intval($_POST ['id_procesador']);
$id_disco = intval($_POST['id_disco']);
$codigodebarras =  $id_articulo .$id_marca .$id_modelo .$id_serie;
$sql = "INSERT INTO materiales (id_articulo, id_marca, id_modelo, id_serie, id_estatus,
 id_procesador, id_disco) VALUES ($id_articulo, $id_marca, $id_modelo, $id_serie, $id_estatus, $id_procesador, $id_disco)";
$query = mysqli_query($con,$sql);
$sqlCE = "INSERT INTO clientesequipos (codigodebarras, id_cliente, id_estatus) VALUES ('$codigodebarras',100, 1)";

if($query !=false){
    $queryCE = mysqli_query($con,$sqlCE);
    if($queryCE !=false){
        echo "<script> alert('Registro  $codigodebarras asignado al cliente 100 - H&S, con estatus 1 - DISPONIBLE  creado correctamente');</script>";

              //Ejecuta la funcion registerAction que se encuentra en el archivo ValidaSesion
              $id_usuario = $_SESSION['id'];
              $accion = "Creación"; // Reemplaza con el tipo de acción (Creación, Actualización, Eliminación, Consulta, etc.)
              $descripcion = "Creación realizada en la página " . $currentPage;
              registerAction($id_usuario, $accion, $descripcion);

    }else{
        echo "<script> alert('Error al asignar equipo: verifique que existe el cliente H&S y el estatus DISPONIBLE'); window.location='Equipos.php';</script>";
};
echo "<script> alert('Registro  $codigodebarras  creado correctamente'); window.location='EquiposDetalles.php?codigodebarras=$codigodebarras';</script>";
}else{ echo "<script> alert('Error, es probable que el registro que intenta ingresar ya exista en la base, verifique y reintentelo');  window.location='Equipos.php';</script>";
};

?>