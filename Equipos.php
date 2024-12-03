<?php
include 'connection.php';
require_once 'ValidaSesion.php';
$con = connection();

// Sanitizar y validar entradas
$busqueda = isset($_POST['busqueda']) ? $con->real_escape_string($_POST['busqueda']) : '';
$page = isset($_POST['page']) ? max(1, (int)$_POST['page']) : 1;
$records_per_page = isset($_POST['records_per_page']) ? max(10, min(100, (int)$_POST['records_per_page'])) : 10;
$offset = ($page - 1) * $records_per_page;

// Preparar la condición WHERE para la búsqueda
$where_conditions = [];
$search_columns = ['M.codigodebarras', 'AR.nom_articulo', 'MA.nom_marca', 'MO.nom_modelo', 'SE.nom_serie', 'ES.nom_estatus', 'PR.nom_procesador', 'DI.tipo'];

foreach ($search_columns as $column) {
    $where_conditions[] = "$column LIKE ?";
}
$where_clause = implode(' OR ', $where_conditions);

// Contar el total de registros
$count_sql = "SELECT COUNT(DISTINCT M.id_material) AS total
FROM materiales M
LEFT JOIN articulos AR ON M.id_articulo = AR.id_articulo
LEFT JOIN marcas MA ON M.id_marca = MA.id_marca
LEFT JOIN modelos MO ON M.id_modelo = MO.id_modelo
LEFT JOIN series SE ON M.id_serie = SE.id_serie
LEFT JOIN estatus ES ON M.id_estatus = ES.id_estatus
LEFT JOIN procesadores PR ON M.id_procesador = PR.id_procesador
LEFT JOIN discos DI ON M.id_disco = DI.id_disco
WHERE $where_clause";

$count_stmt = $con->prepare($count_sql);
if ($count_stmt === false) {
    die("Error en la preparación de la consulta de conteo: " . $con->error);
}

$busqueda_like = "%$busqueda%";
$count_stmt->bind_param(str_repeat('s', count($search_columns)), ...array_fill(0, count($search_columns), $busqueda_like));

if (!$count_stmt->execute()) {
    die("Error en la ejecución de la consulta de conteo: " . $count_stmt->error);
}

$count_result = $count_stmt->get_result();
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);

// Obtener los registros para la tabla actual de Articulos
$sql = "SELECT DISTINCT M.*, AR.nom_articulo, MA.nom_marca, MO.nom_modelo, SE.nom_serie, ES.nom_estatus, PR.nom_procesador, DI.tipo
FROM materiales M
LEFT JOIN articulos AR ON M.id_articulo = AR.id_articulo
LEFT JOIN marcas MA ON M.id_marca = MA.id_marca
LEFT JOIN modelos MO ON M.id_modelo = MO.id_modelo
LEFT JOIN series SE ON M.id_serie = SE.id_serie
LEFT JOIN estatus ES ON M.id_estatus = ES.id_estatus
LEFT JOIN procesadores PR ON M.id_procesador = PR.id_procesador
LEFT JOIN discos DI ON M.id_disco = DI.id_disco
WHERE $where_clause
LIMIT ?, ?";

$stmt = $con->prepare($sql);
if ($stmt === false) {
    die("Error en la preparación de la consulta principal: " . $con->error);
}

$param_types = str_repeat('s', count($search_columns)) . 'ii';
$param_values = array_fill(0, count($search_columns), $busqueda_like);
$param_values[] = $offset;
$param_values[] = $records_per_page;

$stmt->bind_param($param_types, ...$param_values);

if (!$stmt->execute()) {
    die("Error en la ejecución de la consulta principal: " . $stmt->error);
}

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
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Inventarios HardSoft Rent</title>
</head>

<body>
    <?php include 'menu.php'; ?>
    <script src="menujs.js"></script>

    <div class="main-content" id="main-content">
        <button class="openbtn" onclick="toggleMenu()">&#9776; </button>

        <div class="content-h2">
            <h2> Equipos: Gestión de equipos</h2>
        </div>

        <section class="content-grid">

            <div class="form-container">

<form action="" method="POST">
    <h3>Buscar equipo </h3>
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
                                <th>Código de Barras</th>
                                <th>Artículo</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Estatus</th>
                                <th>Editar</th>
                                <th>Eliminar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td><a href='EquiposDetalles.php?codigodebarras=" . urlencode($row['codigodebarras']) . "'>" . htmlspecialchars($row['codigodebarras']) . "</a></td>";
                                    echo "<td>" . htmlspecialchars($row["nom_articulo"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["nom_marca"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["nom_modelo"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["nom_estatus"]) . "</td>";
                                    echo "<td><a href='javascript:confirmUpdateEquipos(\"" . htmlspecialchars($row['codigodebarras']) . "\")'>";
                                    echo '<i class="fas fa-edit"></i> Editar</a></td>';
                                    echo "<td><a href='javascript:confirmDeleteEquipos(\"" . htmlspecialchars($row['codigodebarras']) . "\")'>";
                                    echo '<i class="fas fa-trash"></i> Eliminar</a></td>';
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7'>No se encontraron resultados</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="form-container">

                <H3>Crear nuevo equipo</H3>

                <form action="materiales_insert.php" method="POST">
                    <label for="Articulo">Artículo:</label>
                    <select name="id_articulo" id="Art">
                        <?php
                        $sqlarticulos = "SELECT * FROM articulos ORDER BY id_articulo ASC";
                        $queryarticulos = mysqli_query($con, $sqlarticulos);
                        echo '<option value="" disabled selected>Selecciona una opción</option>';
                        while ($listaarticulos = mysqli_fetch_array($queryarticulos)) {
                            echo '<option value="' . htmlspecialchars($listaarticulos['id_articulo']) . '">' . htmlspecialchars($listaarticulos['id_articulo'] . ' - ' . $listaarticulos['nom_articulo']) . '</option>';
                        }
                        ?>
                    </select>

                    <label for="Marca">Marca:</label>
                    <select name="id_marca">
                        <?php
                        $sqlmarcas = "SELECT * FROM marcas ORDER BY id_marca ASC";
                        $querymarcas = mysqli_query($con, $sqlmarcas);
                        echo '<option value="" disabled selected>Selecciona una opción</option>';
                        while ($listamarcas = mysqli_fetch_array($querymarcas)) {
                            echo '<option value="' . htmlspecialchars($listamarcas['id_marca']) . '">' . htmlspecialchars($listamarcas['id_marca'] . ' - ' . $listamarcas['nom_marca']) . '</option>';
                        }
                        ?>
                    </select>

                    <label for="Modelo">Modelo:</label>
                    <select name="id_modelo">
                        <?php
                        $sqlmodelos = "SELECT * FROM modelos ORDER BY id_modelo ASC";
                        $querymodelos = mysqli_query($con, $sqlmodelos);
                        echo '<option value="" disabled selected>Selecciona una opción</option>';
                        while ($listamodelos = mysqli_fetch_array($querymodelos)) {
                            echo '<option value="' . htmlspecialchars($listamodelos['id_modelo']) . '">' . htmlspecialchars($listamodelos['id_modelo'] . ' - ' . $listamodelos['nom_modelo']) . '</option>';
                        }
                        ?>
                    </select>

                    <label for="Serie">Serie:</label>
                    <select name="id_serie" required>
                        <?php
                        $sqlseries = "SELECT * FROM series ORDER BY id_serie ASC";
                        $queryseries = mysqli_query($con, $sqlseries);
                        echo '<option value="" disabled selected>Selecciona una opción</option>';
                        while ($listaseries = mysqli_fetch_array($queryseries)) {
                            echo '<option value="' . htmlspecialchars($listaseries['id_serie']) . '">' . htmlspecialchars($listaseries['id_serie'] . ' - ' . $listaseries['nom_serie']) . '</option>';
                        }
                        ?>
                    </select>


                    <label for="Estatus">Estatus:</label>
                    <select name="id_estatus" required>
                        <option value="1" readonly> 1 - DISPONIBLE </option>
                    </select>

                    <label for="Procesador">Procesador (nombre - generación - velocidad):</label>
                    <select name="id_procesador" required>
                        <?php
                        $sqlprocesadores = "SELECT * FROM procesadores ORDER BY id_procesador ASC";
                        $queryprocesadores = mysqli_query($con, $sqlprocesadores);
                        echo '<option value="" disabled selected>Selecciona una opción</option>';
                        while ($listaprocesadores = mysqli_fetch_array($queryprocesadores)) {
                            echo '<option value="' . htmlspecialchars($listaprocesadores['id_procesador']) . '">' . htmlspecialchars($listaprocesadores['id_procesador'] . ' - ' . $listaprocesadores['nom_procesador'] . ' - ' . $listaprocesadores['generacion'] . ' - ' . $listaprocesadores['velocidad']) . '</option>';
                        }
                        ?>
                    </select>

                    <label for="Disco">Disco(s) (tipo - entrada - capacidad):</label>
                    <select name="id_disco" required>
                        <?php
                        $sqldiscos = "SELECT * FROM discos ORDER BY id_disco ASC";
                        $querydiscos = mysqli_query($con, $sqldiscos);
                        echo '<option value="" disabled selected>Selecciona una opción</option>';
                        while ($listadiscos = mysqli_fetch_array($querydiscos)) {
                            echo '<option value="' . htmlspecialchars($listadiscos['id_disco']) . '">' . htmlspecialchars($listadiscos['id_disco'] . ' - ' . $listadiscos['tipo'] . ' - ' . $listadiscos['entrada'] . ' - ' . $listadiscos['capacidad']) . '</option>';
                        }
                        ?>
                    </select>
                    <div class="button-container">
                        <input type="submit" value="Guardar">
                        <input type="button" value="Regresar al menu" onClick="location.href='Inicio.php'">
                    </div>
                </form>
            </div>
        </section>
    </div>
</body>

</html>

<script>
    function confirmUpdateEquipos(codigodebarras) {
        if (window.confirm("Vas a editar el registro  \n " + codigodebarras)) {
            window.location.href = "materiales_update.php?codigodebarras=" + encodeURIComponent(codigodebarras);
        }
    }

    function confirmDeleteEquipos(codigodebarras) {
        if (window.confirm("Vas a eliminar el código de barras  \n " + codigodebarras)) {
            window.location.href = "materiales_delete.php?codigodebarras=" + encodeURIComponent(codigodebarras);
        }
    }

    // Función para cambiar de página
    function changePage(page) {
        document.getElementsByName('page')[0].value = page;
        document.forms[0].submit();
    }
</script>