<?php
include('connection.php');
require_once 'ValidaSesion.php';
$con = connection();

// Sanitizar y validar las entradas
$nombre_cliente = isset($_POST['nombre_cliente']) ? $con->real_escape_string(trim($_POST['nombre_cliente'])) : '';
$cliente_rfc = isset($_POST['cliente_rfc']) ? $con->real_escape_string(trim($_POST['cliente_rfc'])) : '';
$cliente_cp = isset($_POST['cliente_cp']) ? $con->real_escape_string(trim($_POST['cliente_cp'])) : '';

// Verificar que todos los campos sean obligatorios
if (empty($nombre_cliente) || empty($cliente_rfc) || empty($cliente_cp)) {
    echo "<script>alert('Por favor, complete todos los campos requeridos.'); window.location='Clientes.php';</script>";
    exit;
}

// Preparar la consulta de inserción
$sql = "INSERT INTO clientes (nombre_cliente, cliente_rfc, cliente_cp) VALUES (?, ?, ?)";
$stmt = $con->prepare($sql);

if ($stmt === false) {
    echo "<script>alert('Error en la preparación de la consulta: " . $con->error . "'); window.location='Clientes.php';</script>";
    exit;
}

// Vincular los parámetros
$stmt->bind_param('sss', $nombre_cliente, $cliente_rfc, $cliente_cp);

// Ejecutar la consulta
if ($stmt->execute()) {
    echo "<script>alert('Cliente registrado correctamente: $nombre_cliente'); window.location='Clientes.php';</script>";

//Ejecuta la funcion registerAction que se encuentra en el archivo ValidaSesion
$id_usuario = $_SESSION['id'];
$accion = "Creación"; // Reemplaza con el tipo de acción (Creación, Actualización, Eliminación, Consulta, etc.)
$descripcion = "Creación realizada en la página " . $currentPage;
registerAction($id_usuario, $accion, $descripcion);


}else
{
    echo "<script>alert('Error al registrar el cliente: " . $stmt->error . "'); window.location='Clientes.php';</script>";
}

// Cerrar la declaración
$stmt->close();
$con->close();
?>
