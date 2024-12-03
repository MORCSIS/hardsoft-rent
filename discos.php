<?php 
include 'connection.php';
require_once 'ValidaSesion.php';
$con = connection();
$con->set_charset("utf8mb4");

// Sanitizar y validar entradas
$busqueda = isset($_GET['busqueda']) ? $con->real_escape_string($_GET['busqueda']) : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$records_per_page = isset($_GET['records_per_page']) ? max(10, min(100, (int)$_GET['records_per_page'])) : 10;
$offset = ($page - 1) * $records_per_page;

// Preparar la consulta para contar registros
$count_sql = "SELECT COUNT(DISTINCT id_disco) AS total FROM discos WHERE 
              LOWER(tipo) LIKE LOWER(?) OR 
              LOWER(entrada) LIKE LOWER(?) OR 
              LOWER(capacidad) LIKE LOWER(?) OR 
              id_disco = ?";
$count_stmt = $con->prepare($count_sql);
$busqueda_like = "%$busqueda%";
$count_stmt->bind_param("ssss", $busqueda_like, $busqueda_like, $busqueda_like, $busqueda);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);

// Preparar la consulta para obtener registros
$sql = "SELECT DISTINCT * FROM discos WHERE 
        LOWER(tipo) LIKE LOWER(?) OR 
        LOWER(entrada) LIKE LOWER(?) OR 
        LOWER(capacidad) LIKE LOWER(?) OR 
        id_disco = ? 
        ORDER BY id_disco DESC LIMIT ?, ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("ssssii", $busqueda_like, $busqueda_like, $busqueda_like, $busqueda, $offset, $records_per_page);
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
    <title>Catálogos: discos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'menu.php'; ?>
    <script src="menujs.js"></script>

<div class="main-content" id="main-content">
<button class="openbtn" onclick="toggleMenu()">&#9776;  </button>

<div class="content-h2">
<h2>   ====> Catálogos: discos</h2>
</div>

<section class="content-grid">
    
<div class="form-container">

<form action="" method="GET">
<h3>Buscar disco</h3>
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
            <th>ID disco</th>
            <th>Tipo</th>
            <th>Entrada</th>
            <th>Capacidad</th>
            <th>Editar</th>
            <th>Eliminar</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row["id_disco"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["tipo"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["entrada"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["capacidad"]) . "</td>";
                echo "<td><a href='javascript:confirmUpdateDiscos(" . htmlspecialchars($row['id_disco']) . ")'>";
                echo '<i class="fas fa-edit"></i> Editar</a></td>';
                echo "<td><a href='javascript:confirmDeleteDiscos(" . htmlspecialchars($row['id_disco']) . ")'>";
                echo '<i class="fas fa-trash"></i> Eliminar</a></td>';
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No se encontraron resultados</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>
</div>

<div class="form-container">

<form id="insertar_disco" method="POST" action="discos_ValidInsert.php" accept-charset="UTF-8">
<h3>Crear disco</h3>

<div class="button-container"> 

<label for="tipo">Tipo de disco:</label>
    <select placeholder="Ingrese tipo de disco"  name="tipo" id="tipo" required autocomplete="off">
    <option value="">Seleccionar tipo de disco</option>
    <option value="SÓLIDO">SÓLIDO</option>
    <option value="MECÁNICO">MECÁNICO</option>
    <option value="NO TIENE">NO TIENE</option>
    <option value="NO APLICA">NO APLICA</option>
</select>
</div>

<div class="button-container"> 
<label for="entrada">Entrada del disco:</label>
<input type="text" placeholder="Ingrese tipo de entrada" name="entrada" id="entrada" required autocomplete="off">
</div>

<div class="button-container"> 
<label for="capacidad">Capacidad del disco:</label>
<input type="number" placeholder="Ingrese capacidad" name="capacidad" id="capacidad" required autocomplete="100" min="1">
<select name="unidad_capacidad" id="unidad_capacidad">
    <option value="GB">GB</option>
    <option value="TB">TB</option>
</select>
</div>
<br>
<hr>
<br>
<div class="button-container"> 
<input type="submit" value="Guardar">
<button type="reset">Restablecer</button>
</div>   

</form>
</div>
</section>
</div>
</body>
</html>
<Script>
function confirmDeleteDiscos(id_disco) {
    if (window.confirm("¿Está seguro que desea eliminar este registro:? \n " + id_disco)) {
    window.location.href = "discos_delete.php?id_disco=" + id_disco;
    }
}

function confirmUpdateDiscos(id_disco) {
    if (window.confirm("Vas a editar el registro  \n " + id_disco)) {
    window.location.href = "discos_update.php?id_disco=" + id_disco;
    }
}
</script>