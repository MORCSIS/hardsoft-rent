<?php
include 'connection.php';
require_once 'ValidaSesion.php';
$con = connection();
$con->set_charset("utf8");


if (isset($_POST['id']) && isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])) {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Encriptar la nueva contraseña

    // Verificar si ya existe otra cuenta con el mismo username o email
    $sql = "SELECT COUNT(*) AS count FROM accounts WHERE (username = ? OR email = ?) AND id != ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ssi", $username, $email, $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        echo '<script type="text/javascript">
                alert("Error: El usuario o email ya existe");
                window.location.href = "accounts.php";
              </script>';
    } else {
        // Si no existe, actualizar la cuenta
        $update_sql = "UPDATE accounts SET username = ?, email = ?, password = ? WHERE id = ?";
        $update_stmt = $con->prepare($update_sql);
        $update_stmt->bind_param("sssi", $username, $email, $password, $id);

        if ($update_stmt->execute()) {
            echo '<script type="text/javascript">
                    alert("Cuenta actualizada correctamente");
                    window.location.href = "accounts.php";
                  </script>';
        } else {
            echo '<script type="text/javascript">
                    alert("Error al actualizar la cuenta");
                    window.location.href = "accounts.php";
                  </script>';
        }
        $update_stmt->close();
    }
    $stmt->close();
}

$con->close();
?>
