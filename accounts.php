<?php 
include 'connection.php';
require_once 'ValidaSesion.php';
$con = connection();

// Sanitizar y validar entradas
$busqueda = isset($_POST['busqueda']) ? $con->real_escape_string($_POST['busqueda']) : '';
$page = isset($_POST['page']) ? max(1, (int)$_POST['page']) : 1;
$records_per_page = isset($_POST['records_per_page']) ? max(10, min(100, (int)$_POST['records_per_page'])) : 10;
$offset = ($page - 1) * $records_per_page;

// Preparar la consulta para contar registros
$count_sql = "SELECT COUNT(DISTINCT id) AS total FROM accounts WHERE username LIKE ? OR email = ?";
$count_stmt = $con->prepare($count_sql);
$busqueda_like = "%$busqueda%";
$count_stmt->bind_param("ss", $busqueda_like, $busqueda);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);

// Preparar la consulta para obtener registros
$sql = "SELECT DISTINCT * FROM accounts WHERE username LIKE ? OR email = ? ORDER BY id DESC LIMIT ?, ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("ssii", $busqueda_like, $busqueda, $offset, $records_per_page);
$stmt->execute();
$result = $stmt->get_result();

//Ejecuta la funcion registerAction que se encuentra en el archivo ValidaSesion
$id_usuario = $_SESSION['id'];
$accion = "Consulta"; // Reemplaza con el tipo de acción (Creación, Actualización, Eliminación, Consulta, etc.)
$descripcion = "Consulta realizada en la página " . $currentPage;
registerAction($id_usuario, $accion, $descripcion);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogos: usuarios</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'menu.php'; ?>
<script src="menujs.js"></script>

<div class="main-content" id="main-content">
<button class="openbtn" onclick="toggleMenu()">&#9776;  </button>

<div class="content-h2">
<h2> Gestion de cuentas: usuarios</h2>
</div>

<section class="content-grid">
    
<div class="form-container">

<form action="" method="POST">
<h3>Buscar usuario</h3>
<div class="button-container"> 
    <input type="text" name="busqueda" value="<?php echo htmlspecialchars($busqueda); ?>" autofocus>
    <input type="submit" value="Buscar"> 
</div>

<!-- Agregar campos ocultos para mantener los valores de paginación -->
<input type="hidden" name="page" value="<?php echo $page; ?>">
<input type="hidden" name="records_per_page" value="<?php echo $records_per_page; ?>">

<div class="pagination">
    <button type="button" <?php if ($page <= 1) echo 'disabled'; ?> 
        onclick="changePage(<?php echo $page - 1; ?>)">
        Anterior
    </button>

    <span>Página <?php echo $page; ?> de <?php echo $total_pages; ?></span>

    <!-- Desplegable para ir directamente a una página -->
    <select onchange="changePage(this.value)">
        <?php
        for ($i = 1; $i <= $total_pages; $i++) {
            echo "<option value='$i' " . ($i == $page ? 'selected' : '') . ">$i</option>";
        }
        ?>
    </select>

    <button type="button" <?php if ($page >= $total_pages) echo 'disabled'; ?> 
        onclick="changePage(<?php echo $page + 1; ?>)">
        Siguiente
    </button>

    <!-- Selector para registros por página -->
    <select class="select-pagination" name="records_per_page" onchange="this.form.submit()">
        <option value="10" <?php echo $records_per_page == 10 ? 'selected' : ''; ?>>10</option>
        <option value="20" <?php echo $records_per_page == 20 ? 'selected' : ''; ?>>20</option>
        <option value="50" <?php echo $records_per_page == 50 ? 'selected' : ''; ?>>50</option>
        <option value="100" <?php echo $records_per_page == 100 ? 'selected' : ''; ?>>100</option>
    </select>
</div>
</form>
<div class="table-container">
    <table>
        <thead>
            <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Apellido Paterno</th>
            <th>Apellido Materno</th>
            <th>Email</th>
            <th>Usuario</th>
            <th>Rol</th>
            <th>fecha_creacion</th>
            <th>Editar</th>
            <th>Eliminar</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["apaterno"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["amaterno"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["username"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["rol"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["fecha_creacion"]) . "</td>";
                echo "<td><a href='javascript:confirmUpdateAccounts(" . htmlspecialchars($row['id']) . ")'>";
                echo '<i class="fas fa-edit"></i> Editar</a></td>';

                // Verificar si el usuario está intentando eliminar su propia cuenta
                if ($row['id'] == $loggedin_user_id) {
                    echo "<td><i class='fas fa-ban'></i> No puedes eliminar tu propia cuenta</td>";
                } else {
                    echo "<td><a href='javascript:confirmDeleteAccounts(" . htmlspecialchars($row['id']) . ")'>";
                    echo '<i class="fas fa-trash"></i> Eliminar</a></td>';
                }
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='8'>No se encontraron resultados</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>
</div>

<div class="form-container">

<form id="insertar_cuenta" method="POST" action="accounts_insert.php">
    <h3>Crear usuario</h3>
    <hr>

    <div class="button-container">
        <label for="name">Nombre(s):</label>
        <input type="text" placeholder="Ingrese su nombre(s)" name="name" required autocomplete="off"
               pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" title="Solo se permiten letras y espacios"><br>
    </div>

    <div class="button-container">
        <label for="apaterno">Apellido paterno:</label>
        <input type="text" placeholder="Ingrese su apellido paterno" name="apaterno" required autocomplete="off"
               pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ]+" title="Solo se permiten letras, sin espacios"><br>
    </div>

    <div class="button-container">
        <label for="amaterno">Apellido materno:</label>
        <input type="text" placeholder="Ingrese su apellido materno" name="amaterno" required autocomplete="off"
               pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ]+" title="Solo se permiten letras, sin espacios"><br>
    </div>
    
    <div class="button-container"> 
    <label for="email">E-mail:</label>
    <input type="email" placeholder="Ingrese email" name="email" required autocomplete="off"><br>
    </div>
<hr>

<div class="button-container"> 
<label for="username">Usuario:</label>
 <input  type="text" placeholder="Ingrese un usuario" name="username" required autocomplete="off"><br>
</div>

<div class="button-container"> 
 <label for="rol">Rol de usuario:</label>
    <select placeholder="Ingrese rol de usuario"  name="rol" id="rol" required autocomplete="off">
    <option value="">Seleccionar rol de usuario</option>
    <option value="1">ADMINISTRADOR</option>
    <option value="2">SUPERVISOR</option>
    <option value="3">LECTURA</option>
</select>
</div>

<div class="button-container"> 
<label for="password">Contraseña:</label>
<input type="password" placeholder="Ingrese contraseña" name="password" required autocomplete="off">
</div>

<div class="button-container"> 
<label for="confpassword">Confirmar contraseña:</label>
 <input type="password" placeholder="Ingrese nuevamente contraseña" name="confpassword" required autocomplete="off">
</div>
<hr>
<div class="button-container"> 
<input type="submit" value="Guardar">
</div>   

</form>
</div>



</section>
</div>

<script>
function confirmDeleteAccounts(id) {
    if (window.confirm("¿Está seguro que desea eliminar esta cuenta:? \n " + id)) {
        window.location.href = "accounts_delete.php?id=" + id;
    }
}

function confirmUpdateAccounts(id) {
    if (window.confirm("Vas a editar la cuenta con ID \n " + id)) {
        window.location.href = "accounts_update.php?id=" + id;
    }
}

document.getElementById("insertar_cuenta").onsubmit = function() {
    var password = document.querySelector('input[name="password"]').value;
    var confpassword = document.querySelector('input[name="confpassword"]').value;

    // Validar que las contraseñas coincidan
    if (password !== confpassword) {
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

// Función para cambiar de página
function changePage(page) {
    document.getElementsByName('page')[0].value = page;
    document.forms[0].submit();
}
</script>
</body>
</html>

