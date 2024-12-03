<?php 
include 'connection.php';
require_once 'ValidaSesion.php';
$con = connection();


// Sanitizar y validar entradas
$busqueda = isset($_GET['busqueda']) ? $con->real_escape_string($_GET['busqueda']) : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$records_per_page = isset($_GET['records_per_page']) ? max(10, min(100, (int)$_GET['records_per_page'])) : 10;
$offset = ($page - 1) * $records_per_page;

// Preparar la consulta para contar registros
$count_sql = "SELECT COUNT(DISTINCT id_serie) AS total FROM series WHERE nom_serie LIKE ? OR id_serie = ?";
$count_stmt = $con->prepare($count_sql);
$busqueda_like = "%$busqueda%";
$count_stmt->bind_param("ss", $busqueda_like, $busqueda);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);

// Preparar la consulta para obtener registros
$sql = "SELECT DISTINCT * FROM series WHERE nom_serie LIKE ? OR id_serie = ? ORDER BY id_serie DESC LIMIT ?, ?";
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
    <title>Catálogos: series</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    

    <?php include 'menu.php'; ?>
    <script src="menujs.js"></script>

<div class="main-content" id="main-content">
<button class="openbtn" onclick="toggleMenu()">&#9776;  </button>

<div class="content-h2">
<h2>   ====> Catálogos: series</h2>
</div>

<section class="content-grid">
    
<div class="form-container">

<form action="" method="GET">
<h3>Buscar serie</h3>
<div class="button-container"> 
 <input type="text" name="busqueda" value="<?php echo htmlspecialchars($busqueda); ?>" autofocus>
<input type="submit" value="Buscar"> 

<select placeholder="registros por página" name="records_per_page" onchange="this.form.submit()">
    <option value="10" <?php echo $records_per_page == 10 ? 'selected' : ''; ?>>10</option>
    <option value="20" <?php echo $records_per_page == 20 ? 'selected' : ''; ?>>20</option>
    <option value="50" <?php echo $records_per_page == 50 ? 'selected' : ''; ?>>50</option>
    <option value="100" <?php echo $records_per_page == 100 ? 'selected' : ''; ?>>100</option>
</select>
</div>
</form>

<div class="pagination">
<button <?php if ($page <= 1) echo 'disabled'; ?> onclick="window.location.href='<?php echo '?page=' . ($page - 1) . '&records_per_page=' . $records_per_page . '&busqueda=' . urlencode($busqueda); ?>'">Anterior</button>
<span>Página <?php echo $page; ?> de <?php echo $total_pages; ?></span>
<button <?php if ($page >= $total_pages) echo 'disabled'; ?> onclick="window.location.href='<?php echo '?page=' . ($page + 1) . '&records_per_page=' . $records_per_page . '&busqueda=' . urlencode($busqueda); ?>'">Siguiente</button>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
            <th>ID serie</th>
            <th>Nombre serie</th>
            <th>Editar</th>
            <th>Eliminar</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row["id_serie"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["nom_serie"]) . "</td>";
                echo "<td><a href='javascript:confirmUpdateSeries(" . htmlspecialchars($row['id_serie']) . ")'>";
                echo '<i class="fas fa-edit"></i> Editar</a></td>';
                echo "<td><a href='javascript:confirmDeleteSeries(" . htmlspecialchars($row['id_serie']) . ")'>";
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

<form id="insertar_serie" method="POST" action="series_ValidInsert.php">
<h3>Crear serie</h3>

<div class="button-container"> 
<input type="text" placeholder="Ingrese serie" name="nom_serie" required autocomplete="off">

<input type="submit" value="Guardar">
</div>   

</form>
</div>
</section>
</div>
</body>
</html>
<script>
function confirmDeleteSeries(id_serie) {
    if (window.confirm("¿Está seguro que desea eliminar este registro:? \n " + id_serie)) {
    window.location.href = "series_delete.php?id_serie=" + id_serie;
    }
}

function confirmUpdateSeries(id_serie) {
    if (window.confirm("Vas a editar el registro  \n " + id_serie)) {
    window.location.href = "series_update.php?id_serie=" + id_serie;
    }
}
</script>