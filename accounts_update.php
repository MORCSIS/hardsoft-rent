<?php 
include 'connection.php';
require_once 'ValidaSesion.php';
$con = connection();
$con->set_charset("utf8");

$id = $_GET['id'];

// Consulta para obtener los datos de la cuenta y su rol
$sql = "SELECT DISTINCT a.*, r.nom_rol FROM accounts a LEFT JOIN roles r ON a.rol = r.id_rol WHERE a.id = '$id'";
$query = $con->query($sql);
$cuenta = $query->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil: Actualizar datos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <?php include 'menu.php'; ?>
    <script src="menujs.js"></script>

<div class="main-content" id="main-content">
<button class="openbtn" onclick="toggleMenu()">&#9776;</button>

<h2>Catálogos: Cuentas</h2>

<section class="content-grid">
    <div class="form-container">
        <form id="editar_cuenta" method="POST" action="accounts_edit.php">
            <h3>Actualizar datos de usuario</h3>
            <hr>

            <div class="button-container"> 
                <label for="name">Nombre(s):</label>
                <input type="text" value="<?= htmlspecialchars($cuenta['name'] ?? ''); ?>" name="name" required autocomplete="off"
                pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" title="Solo se permiten letras y espacios"><br>
            
            </div>

            <div class="button-container"> 
                <label for="apaterno">Apellido paterno:</label>
                <input type="text" value="<?= htmlspecialchars($cuenta['apaterno'] ?? ''); ?>" name="apaterno" required autocomplete="off"
                pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ]+" title="Solo se permiten letras, sin espacios"><br>
            </div>

            <div class="button-container"> 
                <label for="amaterno">Apellido materno:</label>
                <input type="text" value="<?= htmlspecialchars($cuenta['amaterno'] ?? ''); ?>" name="amaterno" required autocomplete="off"
                pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ]+" title="Solo se permiten letras, sin espacios"><br>
            </div>

            <div class="button-container"> 
                <label for="email">E-mail:</label>
                <input type="email" value="<?= htmlspecialchars($cuenta['email'] ?? ''); ?>" name="email" required autocomplete="off"><br>
            </div>

            <hr>

            <div class="button-container"> 
                <label for="username">Usuario:</label>
                <input type="text" value="<?= htmlspecialchars($cuenta['username'] ?? ''); ?>" name="username" required autocomplete="off"><br>
            </div>

            <div class="button-container"> 
                <label for="rol">Rol de usuario:</label>
                <select name="rol" id="rol" required>
                    <option value="1" <?= ($cuenta['rol'] == 1) ? 'selected' : ''; ?>>ADMINISTRADOR</option>
                    <option value="2" <?= ($cuenta['rol'] == 2) ? 'selected' : ''; ?>>SUPERVISOR</option>
                    <option value="3" <?= ($cuenta['rol'] == 3) ? 'selected' : ''; ?>>LECTURA</option>
                </select>
            </div>

            <div class="button-container"> 
                <label for="password">Contraseña:</label>
                <input type="password" placeholder="Usa la misma si no quieres cambiarla" name="password" autocomplete="off">
            </div>

            <div class="button-container"> 
                <label for="confpassword">Confirmar contraseña:</label>
                <input type="password" placeholder="Confirma la misma que el campo anterior" name="confpassword" autocomplete="off">
            </div>

            <hr>
            <div class="button-container"> 
            <input type="submit" value="Guardar">
            <input type="button" value="Cancelar" onClick="location.href='accounts.php'">   
            </div>   
        </form>
    </div>
</section>
</div>
</body>
</html>

<script>
// Validación de contraseñas
document.getElementById("editar_cuenta").onsubmit = function() {
    var password = document.querySelector('input[name="password"]').value;
    var confpassword = document.querySelector('input[name="confpassword"]').value;

    // Si las contraseñas no coinciden
    if (password && password !== confpassword) {
        alert("Error: Las contraseñas no coinciden.");
        return false;
    }

// Validar que la contraseña cumpla con los requisitos
var regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>/?]).{10,}$/;
if (!regex.test(password)) {
    alert("Error: La contraseña debe tener al menos 10 caracteres, incluyendo una mayúscula, una minúscula, un número y un carácter especial.");
    return false;
}

return true;
};
</script>
