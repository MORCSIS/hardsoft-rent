<?php 
include 'ValidaSesion.php'; 
include 'ce_engine.php';
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

<h2>Equipos: Asignados a clientes</h2>

<section class="content-grid">
    
<div class="form-container">

<form action="" method="GET">
<h3>Buscar cliente</h3>

<div class="button-container"> 
 <input type="text" name="busqueda" value="<?php echo htmlspecialchars($busqueda); ?>" autofocus>
<input type="submit" value="Buscar"> 
</div>

<div class="pagination">
    <button <?php if ($page <= 1) echo 'disabled'; ?> 
        onclick="window.location.href='<?php echo '?page=' . ($page - 1) . '&records_per_page=' . $records_per_page . '&busqueda=' . $busqueda; ?>'">
        Anterior
    </button>

    <span>Página <?php echo $page; ?> de <?php echo $total_pages; ?></span>

    <!-- Desplegable para ir directamente a una página -->
    <select onchange="window.location.href='?page=' + this.value + '&records_per_page=<?php echo $records_per_page; ?>&busqueda=<?php echo $busqueda; ?>'">
        <?php
        for ($i = 1; $i <= $total_pages; $i++) {
            echo "<option value='$i' " . ($i == $page ? 'selected' : '') . ">$i</option>";
        }
        ?>
    </select>

    <button <?php if ($page >= $total_pages) echo 'disabled'; ?> 
        onclick="window.location.href='<?php echo '?page=' . ($page + 1) . '&records_per_page=' . $records_per_page . '&busqueda=' . $busqueda; ?>'">
        Siguiente
    </button>

    <!-- Selector para registros por página -->
    <select class="select-pagination" name="records_per_page" onchange="window.location.href='<?php echo '?page=1&records_per_page='; ?>' + this.value + '&busqueda=<?php echo $busqueda; ?>'">
        <option value="10" <?php echo $records_per_page == 10 ? 'selected' : ''; ?>>10</option>
        <option value="20" <?php echo $records_per_page == 20 ? 'selected' : ''; ?>>20</option>
        <option value="50" <?php echo $records_per_page == 50 ? 'selected' : ''; ?>>50</option>
        <option value="100" <?php echo $records_per_page == 100 ? 'selected' : ''; ?>>100</option>
    </select>
</div>

</form>



<!-- MOSTRAR TABLA CON CLIENTE SELECCIONADO -->
    <div class="table-container">    
    <table> 
        <thead>
            <tr>
            <th>ID Cliente</th>
            <th>ID Empresa</th>
            <th>Nombre del cliente</th>
            <th>RFC del cliente</th>
            <th>CP del cliente</th>
            </tr>
        </thead>
        <tbody>
        <?php
            if ($result1->num_rows > 0) {
                while ($row1 = $result1->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row1["id_cliente"] . "</td>";
                    echo "<td>" . $row1["id_empresa"] . "</td>";
                    echo "<td>" . $row1["nombre_cliente"] . "</td>";
                    echo "<td>" . $row1["cliente_rfc"] . "</td>";
                    echo "<td>" . $row1["cliente_cp"] . "</td>";
                    echo "</tr>";
    
                }} else {
                    echo "<tr><td colspan='14'>No se encontraron resultados</td></tr>";
            }
            ?>
            </tbody>
    </table>
    </div>
</div>

<div class="table-container">

<!-- MOSTRAR TABLA CON ARTICULOS ASIGNADOS AL CLIENTE -->
    
        <table>
        <thead>
            <tr>
            <th>Código de barras</th>
            <th>Equipo</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Serie</th>
            <th>Estatus del equipo</th>
        </tr>
    </thead>
        <tbody>
        <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["codigodebarras"] . "</td>";
                    echo "<td>" . $row["nom_articulo"] . "</td>";
                    echo "<td>" . $row["nom_marca"] . "</td>";
                    echo "<td>" . $row["nom_modelo"] . "</td>";
                    echo "<td>" . $row["nom_serie"] . "</td>";
                    echo "<td>" . $row["nom_estatus"] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='14'>No se encontraron resultados</td></tr>";
            }
                ?>
            </tbody>
    </table>
    </div>
</section>
</div>
</body>
</html>