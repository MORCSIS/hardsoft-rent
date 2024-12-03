<?php
include 'connection.php';
require_once 'ValidaSesion.php';

$conexion = connection();


// Manejo de la solicitud POST para cambiar la contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = $_SESSION['id'];
    $password_antigua = $_POST['password_antigua'];
    $password_nueva = $_POST['password_nueva'];
    $password_nueva_repetida = $_POST['password_nueva_repetida'];

    // Verificar si la nueva contraseña coincide con su repetición
    if ($password_nueva !== $password_nueva_repetida) {
        echo "<script>alert('Error: Las contraseñas no coinciden.');</script>";
    } else {
        // Verificar la longitud y los caracteres de la nueva contraseña
        // Actualizamos la expresión regular para que funcione correctamente
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&.])[A-Za-z\d@$!%*?&.]{10,}$/', $password_nueva)) {
            echo "<script>alert('Error: La nueva contraseña debe tener al menos 10 caracteres, incluyendo una mayúscula, una minúscula, un número y un carácter especial.');</script>";
        } else {
            // Validar la contraseña anterior
            $stmt = $conexion->prepare('SELECT password FROM accounts WHERE id = ?');
            $stmt->bind_param('i', $id_usuario);
            $stmt->execute();
            $stmt->bind_result($password_actual);
            $stmt->fetch();
            $stmt->close();

            // Comparar la contraseña anterior con la ingresada
            if (md5($password_antigua) === $password_actual) {
                // Actualizar la contraseña
                $password_nueva_hash = md5($password_nueva);
                $stmt = $conexion->prepare('UPDATE accounts SET password = ? WHERE id = ?');
                $stmt->bind_param('si', $password_nueva_hash, $id_usuario);
                if ($stmt->execute()) {
                    echo "<script>alert('Contraseña actualizada correctamente.'); window.location='Inicio.php';</script>";
                } else {
                    echo "<script>alert('Error al actualizar la contraseña.');</script>";
                }
                $stmt->close();
            } else {
                echo "<script>alert('Error: La contraseña anterior es incorrecta.');</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogos: Cuentas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <?php include 'menu.php'; ?>
    <script src="menujs.js"></script>

<div class="main-content" id="main-content">
<button class="openbtn" onclick="toggleMenu()">&#9776;  </button>

<h2>Cambiar Contraseña</h2>

<section class="content-grid">
    
<div class="form-container">
    <form method="POST" onsubmit="return validarFormulario();">
        <label for="password_antigua">Contraseña anterior:</label><br>
        <input type="password" id="password_antigua" name="password_antigua" required autocomplete="FALSE"><br><br>
<hr>
<div>
    <p>La nueva contraseña debe tener al menos 10 caracteres, incluyendo una mayúscula, 
        una minúscula, un número y un carácter especial.</p> </div><br><br>
        <label for="password_nueva">Nueva contraseña:</label><br>
        <input type="password" id="password_nueva" name="password_nueva" required autocomplete="FALSE><br><br>

        <label for="password_nueva_repetida">Repetir nueva contraseña:</label><br>
        <input type="password" id="password_nueva_repetida" name="password_nueva_repetida" required autocomplete="FALSE><br><br>
<hr>
        <div class="button-container"> 
        <button type="submit">Guardar</button>
        <button type="button" onclick="window.location.href='Inicio.php'">Cancelar</button>
        </div>
    </form>
</div>
</section>
</div>
</body>
</html>

<script>
        function validarFormulario() {
            var nuevaContraseña = document.getElementById("password_nueva").value;
            var repetirContraseña = document.getElementById("password_nueva_repetida").value;

            // Validar que las contraseñas coincidan
            if (nuevaContraseña !== repetirContraseña) {
                alert("Error: Las contraseñas no coinciden.");
                return false;
            }

            // Actualizamos la expresión regular para permitir más caracteres especiales, incluyendo '.'
            var regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&.])[A-Za-z\d@$!%*?&.]{10,}$/;
            if (!regex.test(nuevaContraseña)) {
                alert("Error: La nueva contraseña debe tener al menos 10 caracteres, incluyendo una mayúscula, una minúscula, un número y un carácter especial.");
                return false;
            }

            return true;
        }
    </script>

