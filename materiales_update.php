<?php
include 'connection.php';
require_once 'ValidaSesion.php';
$con = connection();
$busqueda = isset($_GET['codigodebarras']) ? $_GET['codigodebarras'] : '';

// Obtener los registros para el detalle del artículo
$sql = "SELECT DISTINCT *
        FROM materiales M
        LEFT JOIN articulos AR ON M.id_articulo = AR.id_articulo
        LEFT JOIN marcas MA ON M.id_marca = MA.id_marca
        LEFT JOIN modelos MO ON M.id_modelo = MO.id_modelo
        LEFT JOIN series SE ON M.id_serie = SE.id_serie
        LEFT JOIN estatus ES ON M.id_estatus = ES.id_estatus
        LEFT JOIN procesadores PR ON M.id_procesador = PR.id_procesador
        LEFT JOIN discos DI ON M.id_disco = DI.id_disco
        WHERE M.codigodebarras = '$busqueda'";

$result = $con->query($sql);
$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <title>Gestión de equipos: Editar equipo</title>
</head>

<body>
    <?php include 'menu.php'; ?>
    <script src="menujs.js"></script>

    <div class="main-content" id="main-content">
        <button class="openbtn" onclick="toggleMenu()">&#9776;</button>

        <h2>Gestión de equipos: Actualizar equipo</h2>
        <section class="content-grid">
            <div class="form-container">
                <h3>Datos actuales del equipo</h3>
                <?php if ($row): ?>
                    <form action="" method="GET">
                  
                        <!-- Código de barras -->
                        <div class="form-group">
                            <svg id="barcode"></svg><hr>
                            <label for="codigodebarras">Código de Barras:</label>
                            <input type="text" name="codigodebarras" value="<?= htmlspecialchars(trim($row['codigodebarras'])); ?>" readonly>
                        </div>

                        <!-- Campos de solo lectura adicionales -->
                        <div class="form-group">
                            <label for="nom_articulo">Nombre del Artículo:</label>
                            <input type="text" name="nom_articulo" value="<?= htmlspecialchars(trim($row['nom_articulo'])); ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label for="nom_marca">Marca:</label>
                            <input type="text" name="nom_marca" value="<?= htmlspecialchars(trim($row['nom_marca'])); ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label for="nom_modelo">Modelo:</label>
                            <input type="text" name="nom_modelo" value="<?= htmlspecialchars(trim($row['nom_modelo'])); ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label for="nom_serie">Serie:</label>
                            <input type="text" name="nom_serie" value="<?= htmlspecialchars(trim($row['nom_serie'])); ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label for="nom_estatus">Estatus:</label>
                            <input type="text" name="nom_estatus" value="<?= htmlspecialchars(trim($row['nom_estatus'])); ?>" readonly>
                        </div>
                    </form>
                <?php else: ?>
                    <p>No se encontró el artículo solicitado.</p>
                <?php endif; ?>
            </div>

            <div class="form-container">
                <h3>Datos nuevos para el equipo</h3>
                <form action="materiales_edit.php" method="POST">
                    <input type="hidden" name="codigoant" value="<?= htmlspecialchars($busqueda); ?>">

                    <div class="form-group">
                        <label for="Articulo">Articulo:</label>
                        <select name="id_articulo" required>
                            <?php
                            $sqlarticulos = "SELECT * FROM articulos ORDER BY id_articulo ASC";
                            $queryarticulos = mysqli_query($con, $sqlarticulos);
                            echo '<option value="" disabled>Selecciona una opción</option>';
                            while ($listaarticulos = mysqli_fetch_array($queryarticulos)) {
                                $selected = ($listaarticulos['id_articulo'] == $row['id_articulo']) ? 'selected' : '';
                                echo '<option value="' . $listaarticulos['id_articulo'] . '" ' . $selected . '>' . $listaarticulos['id_articulo'] . ' - ' . $listaarticulos['nom_articulo'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="Marca">Marca:</label>
                        <select name="id_marca" required>
                            <?php
                            $sqlmarcas = "SELECT * FROM marcas ORDER BY id_marca ASC";
                            $querymarcas = mysqli_query($con, $sqlmarcas);
                            echo '<option value="" disabled>Selecciona una opción</option>';
                            while ($listamarcas = mysqli_fetch_array($querymarcas)) {
                                $selected = ($listamarcas['id_marca'] == $row['id_marca']) ? 'selected' : '';
                                echo '<option value="' . $listamarcas['id_marca'] . '" ' . $selected . '>' . $listamarcas['id_marca'] . ' - ' . $listamarcas['nom_marca'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="Modelo">Modelo:</label>
                        <select name="id_modelo" required>
                            <?php
                            $sqlmodelos = "SELECT * FROM modelos ORDER BY id_modelo ASC";
                            $querymodelos = mysqli_query($con, $sqlmodelos);
                            echo '<option value="" disabled>Selecciona una opción</option>';
                            while ($listamodelos = mysqli_fetch_array($querymodelos)) {
                                $selected = ($listamodelos['id_modelo'] == $row['id_modelo']) ? 'selected' : '';
                                echo '<option value="' . $listamodelos['id_modelo'] . '" ' . $selected . '>' . $listamodelos['id_modelo'] . ' - ' . $listamodelos['nom_modelo'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="Serie">Serie:</label>
                        <select name="id_serie" required>
                            <?php
                            $sqlseries = "SELECT * FROM series ORDER BY id_serie ASC";
                            $queryseries = mysqli_query($con, $sqlseries);
                            echo '<option value="" disabled>Selecciona una opción</option>';
                            while ($listaseries = mysqli_fetch_array($queryseries)) {
                                $selected = ($listaseries['id_serie'] == $row['id_serie']) ? 'selected' : '';
                                echo '<option value="' . $listaseries['id_serie'] . '" ' . $selected . '>' . $listaseries['id_serie'] . ' - ' . $listaseries['nom_serie'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="Procesador">Procesador (nombre - generación - velocidad):</label>
                        <select name="id_procesador" required>
                            <?php
                            $sqlprocesadores = "SELECT * FROM procesadores ORDER BY id_procesador ASC";
                            $queryprocesadores = mysqli_query($con, $sqlprocesadores);
                            echo '<option value="" disabled>Selecciona una opción</option>';
                            while ($listaprocesadores = mysqli_fetch_array($queryprocesadores)) {
                                $selected = ($listaprocesadores['id_procesador'] == $row['id_procesador']) ? 'selected' : '';
                                echo '<option value="' . $listaprocesadores['id_procesador'] . '" ' . $selected . '>' . $listaprocesadores['id_procesador'] . ' - ' . $listaprocesadores['nom_procesador'] . ' - ' . $listaprocesadores['generacion'] . ' - ' . $listaprocesadores['velocidad'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                    <label for="Disco">Disco(s) (tipo - entrada - capacidad):</label>
                    <select name="id_disco" required>
                        <?php
                        $sqldiscos = "SELECT * FROM discos ORDER BY id_disco ASC";
                        $querydiscos = mysqli_query($con, $sqldiscos);
                        echo '<option value="" disabled>Selecciona una opción</option>';
                        while ($listadiscos = mysqli_fetch_array($querydiscos)) {
                            $selected = ($listadiscos['id_disco'] == $row['id_disco']) ? 'selected' : '';
                            echo '<option value="' . $listadiscos['id_disco'] . '" ' . $selected . '>' . $listadiscos['id_disco'] . ' - ' . $listadiscos['tipo'] . ' - ' . $listadiscos['entrada'] . ' - ' . $listadiscos['capacidad'] . '</option>';
                        }
                        ?>
                    </select>
                    </div>

                    <div class="button-container">
                        <input type="submit" value="Actualizar">
                        <input type="button" value="Cancelar" onClick="location.href='Equipos.php'">
                    </div>
                </form>
            </div>
        </section>
    </div>

<script>
    const barcodeValue = "<?= htmlspecialchars($row['codigodebarras'] ?? ''); ?>";
    if (barcodeValue) {
        JsBarcode("#barcode", barcodeValue, {
            format: "CODE128",
            width: 2,
            height: 50,
            displayValue: true
        });
    }

    function printBarcode() {
        const barcodeContent = document.getElementById('barcode').outerHTML;
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
                <head><title>Imprimir Código de Barras</title></head>
                <body style="text-align: center; padding-top: 50px;">
                    ${barcodeContent}
                    <script>
                        window.onload = function() {
                            window.print();
                            window.onafterprint = function() { window.close(); };
                        };
                    <\/script>
                </body>
            </html>
        `);
        printWindow.document.close();
    }
</script>
</body>
</html>
