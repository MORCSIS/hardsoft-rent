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
$count_sql = "SELECT COUNT(DISTINCT id_articulo) AS total FROM articulos WHERE nom_articulo LIKE ? OR id_articulo = ?";
$count_stmt = $con->prepare($count_sql);
$busqueda_like = "%$busqueda%";
$count_stmt->bind_param("ss", $busqueda_like, $busqueda);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);

// Preparar la consulta para obtener registros
$sql = "SELECT DISTINCT * FROM articulos WHERE nom_articulo LIKE ? OR id_articulo = ? ORDER BY id_articulo DESC LIMIT ?, ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("ssii", $busqueda_like, $busqueda, $offset, $records_per_page);
$stmt->execute();
$result = $stmt->get_result();

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
    <title>Catálogos: artículos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'menu.php'; ?>
<script src="menujs.js"></script>

<div class="main-content" id="main-content">
<button class="openbtn" onclick="toggleMenu()">&#9776;  </button>

<div class="content-h2">
<h2> Catálogos: artículos</h2>
</div>

<section class="content-grid">
    
<div class="form-container">

<form action="" method="POST">
<h3>Buscar artículo</h3>
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
            <th>ID artículo</th>
            <th>Nombre artículo</th>
            <th>Editar</th>
            <th>Eliminar</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row["id_articulo"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["nom_articulo"]) . "</td>";
                echo "<td><a href='javascript:confirmUpdateArticulos(" . htmlspecialchars($row['id_articulo']) . ")'>";
                echo '<i class="fas fa-edit"></i> Editar</a></td>';
                echo "<td><a href='javascript:confirmDeleteArticulos(" . htmlspecialchars($row['id_articulo']) . ")'>";
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

<form id="insertar_articulo" method="POST" action="articulos_ValidInsert.php">
<h3>Crear artículo</h3>

<div class="button-container"> 
    <input type="text" placeholder="Ingrese artículo" name="nom_articulo" required autocomplete="off">
    <input type="submit" value="Guardar">
    <input type="button" value="Carga masiva" onclick="location.href='articulos_carga_masiva.php'">

    <a href="" class="btn"></a>
</div>   

</form>
</div>
</section>
</div>

<script>
function confirmDeleteArticulos(id_articulo) {
    if (window.confirm("¿Está seguro que desea eliminar este registro:? \n " + id_articulo)) {
        window.location.href = "articulos_delete.php?id_articulo=" + id_articulo;
    }
}

function confirmUpdateArticulos(id_articulo) {
    if (window.confirm("Vas a editar el registro  \n " + id_articulo)) {
        window.location.href = "articulos_update.php?id_articulo=" + id_articulo;
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
