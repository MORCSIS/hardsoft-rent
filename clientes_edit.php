<?php
include 'connection.php';
require_once 'ValidaSesion.php';
$con = connection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir y sanitizar datos de entrada
    $id_cliente = mysqli_real_escape_string($con, $_POST['id_cliente']);
    $id_empresa = mysqli_real_escape_string($con, $_POST['id_empresa']);
    $nombre_cliente = mysqli_real_escape_string($con, $_POST['nombre_cliente']);
    $cliente_rfc = mysqli_real_escape_string($con, $_POST['cliente_rfc']);
    $cliente_cp = mysqli_real_escape_string($con, $_POST['cliente_cp']);

    // Validación de RFC (13 caracteres alfanuméricos)
    if (!preg_match('/^[A-Za-z0-9]{13}$/', $cliente_rfc)) {
        die("Error: El RFC debe contener exactamente 13 caracteres alfanuméricos.");
    }

    // Validación de Código Postal (5 dígitos numéricos)
    if (!preg_match('/^[0-9]{5}$/', $cliente_cp)) {
        die("Error: El Código Postal debe contener exactamente 5 dígitos.");
    }

    // Preparar la consulta SQL para evitar SQL Injection
    $sql = "UPDATE clientes SET 
                id_empresa = ?, 
                nombre_cliente = ?, 
                cliente_rfc = ?, 
                cliente_cp = ? 
            WHERE id_cliente = ?";

    $stmt = $con->prepare($sql);
    $stmt->bind_param("isssi", $id_empresa, $nombre_cliente, $cliente_rfc, $cliente_cp, $id_cliente);

    // Ejecutar la consulta y verificar el resultado
    if ($stmt->execute()) {
        echo "<script>alert('Cliente actualizado correctamente.'); window.location.href = 'Clientes.php';</script>";
    } else {
        echo "<script>alert('Error al actualizar el cliente: " . htmlspecialchars($stmt->error) . "');</script>";
    }

    // Cerrar la conexión y la declaración
    $stmt->close();
    $con->close();
} else {
    echo "<script>alert('Solicitud no válida.'); window.location.href = 'Clientes.php';</script>";
}
?>
