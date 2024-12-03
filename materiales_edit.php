<?php
include('connection.php');
require_once 'ValidaSesion.php';
$con = connection();

$id_articulo = intval($_POST ['id_articulo']);
$id_marca = intval($_POST ['id_marca']);
$id_modelo = intval($_POST ['id_modelo']);
$id_serie = intval($_POST ['id_serie']);
$id_procesador = intval($_POST ['id_procesador']);
$id_disco = intval($_POST['id_disco']);
$codigodebarras =  $id_articulo. $id_marca . $id_modelo .$id_serie;
$codigoanterior = intval($_POST['codigoant']);

$sql = "UPDATE materiales SET id_articulo = $id_articulo, id_marca = $id_marca, id_modelo = $id_modelo, id_serie = $id_serie, id_procesador = $id_procesador, id_disco = $id_disco  WHERE codigodebarras = '$codigoanterior'";

$query = mysqli_query($con,$sql);

$sqlCE = "UPDATE clientesequipos SET codigodebarras = '$codigodebarras' WHERE codigodebarras = '$codigoanterior'";

if($query !=false){
    $queryCE = mysqli_query($con,$sqlCE);
    if($queryCE !=false){
        echo "<script> alert('Código anterior:  $codigoanterior cambió a $codigodebarras. Actualizado correctamente, el estatus y cliente asignado NO fueron modificados');</script>";
    }else{
        echo "<script> alert('Error al actualizar equipo: verifique los datos y vuelva a intentarlo'); window.location='Equipos.php';</script>";
};
echo "<script> alert('Registro anterior : $codigoanterior cambió a $codigodebarras . Actualizado correctamente'); window.location='EquiposDetalles.php?codigodebarras=$codigodebarras';</script>";
}else{ echo "<script> alert('Error, es probable que el registro que intenta ingresar ya exista en la base, verifique y reintentelo');  window.location='Equipos.php';</script>";
};

?>