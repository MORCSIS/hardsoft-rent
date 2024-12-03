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
$count_sql = "SELECT COUNT(DISTINCT id_marca) AS total FROM marcas WHERE nom_marca LIKE ? OR id_marca = ?";
$count_stmt = $con->prepare($count_sql);
$busqueda_like = "%$busqueda%";
$count_stmt->bind_param("ss", $busqueda_like, $busqueda);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);

// Preparar la consulta para obtener registros
$sql = "SELECT DISTINCT * FROM marcas WHERE nom_marca LIKE ? OR id_marca = ? ORDER BY id_marca DESC LIMIT ?, ?";
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
    <title>Catálogos: marcas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'menu.php'; ?>
<script src="menujs.js"></script>

<div class="main-content" id="main-content">
<button class="openbtn" onclick="toggleMenu()">&#9776;  </button>

<div class="content-h2">
<h2> Catálogos: marcas</h2>
</div>

<section class="content-grid">
    
<div class="form-container">

<form action="" method="POST">
<h3>Buscar marca</h3>
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
            <th>ID marca</th>
            <th>Nombre marca</th>
            <th>Editar</th>
            <th>Eliminar</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row["id_marca"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["nom_marca"]) . "</td>";
                echo "<td><a href='javascript:confirmUpdatemarcas(" . htmlspecialchars($row['id_marca']) . ")'>";
                echo '<i class="fas fa-edit"></i> Editar</a></td>';
                echo "<td><a href='javascript:confirmDeletemarcas(" . htmlspecialchars($row['id_marca']) . ")'>";
                echo '<i class="fas fa-trash"></i> Eliminar</a></td>';
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No se encontraron resultados</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>
</div>

<div class="form-container">

<form id="insertar_marca" method="POST" action="marcas_ValidInsert.php">
<h3>Crear marca</h3>

<div class="button-container"> 
    <input type="text" placeholder="Ingrese marca" name="nom_marca" required autocomplete="off">
    <input type="submit" value="Guardar">
    <input type="button" value="Carga masiva" onclick="location.href='marcas_carga_masiva.php'">

    <a href="" class="btn"></a>
</div>   

</form>
</div>
</section>
</div>

<script>
function confirmDeletemarcas(id_marca) {
    if (window.confirm("¿Está seguro que desea eliminar este registro:? \n " + id_marca)) {
        window.location.href = "marcas_delete.php?id_marca=" + id_marca;
    }
}

function confirmUpdatemarcas(id_marca) {
    if (window.confirm("Vas a editar el registro  \n " + id_marca)) {
        window.location.href = "marcas_update.php?id_marca=" + id_marca;
    }
}

// Función para cambiar de página
function changePage(page) {
    document.getElementsByName('page')[0].value = page;
    document.forms[0].submit();
}
</script>
</body>
</html>
