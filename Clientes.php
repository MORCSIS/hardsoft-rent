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
$search_columns = ['id_cliente', 'nombre_cliente', 'cliente_rfc', 'cliente_cp'];

foreach ($search_columns as $column) {
    $where_conditions[] = "$column LIKE ?";
}
$where_clause = implode(' OR ', $where_conditions);

// Contar el total de registros
$count_sql = "SELECT COUNT(*) AS total FROM clientes WHERE $where_clause";

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

// Obtener los registros para la tabla actual de Clientes
$sql = "SELECT *, id_empresa FROM clientes WHERE $where_clause LIMIT ?, ?"; // Cambiar aquí

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
    <title>Clientes: Gestión de Clientes</title>
</head>

<body>
    <?php include 'menu.php'; ?>
    <script src="menujs.js"></script>

    <div class="main-content" id="main-content">
        <button class="openbtn" onclick="toggleMenu()">&#9776; </button>

        <div class="content-h2">
            <h2> Clientes: Gestión de Clientes</h2>
        </div>

        <section class="content-grid">

            <div class="form-container">

                <form action="" method="POST">
                    <h3>Buscar cliente </h3>
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
                                <th>ID Cliente</th>
                                <th>Nombre Cliente</th>
                                <th>RFC</th>
                                <th>CP</th>
                                <th>ID Empresa</th> <!-- Nuevo encabezado para id_empresa -->
                                <th>Editar</th>
                                <th>Eliminar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['id_cliente']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["nombre_cliente"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["cliente_rfc"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["cliente_cp"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["id_empresa"]) . "</td>"; // Mostrar id_empresa
                                    echo "<td><a href='javascript:confirmUpdateClientes(\"" . htmlspecialchars($row['id_cliente']) . "\")'>";
                                    echo '<i class="fas fa-edit"></i> Editar</a></td>';
                                    echo "<td><a href='javascript:confirmDeleteClientes(\"" . htmlspecialchars($row['id_cliente']) . "\")'>";
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
                <h3>Crear nuevo cliente</h3>
                <form action="clientes_insert.php" method="POST">
                    <label for="nombre_cliente">Nombre Cliente:</label>
                    <input type="text" name="nombre_cliente" required>

                    <label for="cliente_rfc">RFC:</label>
                    <input type="text" name="cliente_rfc" maxlength="13" required>

                    <label for="cliente_cp">Código Postal:</label>
                    <input type="text" name="cliente_cp" maxlength="5" required>

                    <label for="id_empresa">ID Empresa:</label> <!-- Nuevo campo para id_empresa -->
                    <input type="text" name="id_empresa" required>

                    <div class="button-container">
                        <input type="submit" value="Guardar">
                        <input type="button" value="Regresar al menú" onClick="location.href='Inicio.php'">
                    </div>
                </form>
            </div>
        </section>
    </div>
</body>

</html>

<script>
    function confirmUpdateClientes(id_cliente) {
        if (window.confirm("Vas a editar el cliente con ID  \n " + id_cliente)) {
            window.location.href = "clientes_update.php?id_cliente=" + encodeURIComponent(id_cliente);
        }
    }

    function confirmDeleteClientes(id_cliente) {
        if (window.confirm("Vas a eliminar el cliente con ID  \n " + id_cliente)) {
            window.location.href = "clientes_delete.php?id_cliente=" + encodeURIComponent(id_cliente);
        }
    }

    // Función para cambiar de página
    function changePage(page) {
        document.getElementsByName('page')[0].value = page;
        document.forms[0].submit();
    }
</script>
