<?php
include 'connection.php';
require_once 'ValidaSesion.php';
$con = connection();
$busqueda = isset($_GET['codigodebarras']) ? trim($_GET['codigodebarras']) : '';

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
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <title>Detalle del Artículo</title>
</head>

<body>
    <?php include 'menu.php'; ?>
    <script src="menujs.js"></script>

<div class="main-content" id="main-content">
    <button class="openbtn" onclick="toggleMenu()">&#9776;</button>

    <h2>Detalle del Equipo</h2>
    <section class="content-grid">
        <div class="form-container">

        <h3>Datos básicos</h3>
            <?php if ($row): ?>
                <form action="" method="GET">
                <div class="button-container">  
                        <input type="button" value="Regresar" onClick="location.href='Equipos.php'">
                        <input type="button" value="Imprimir" onClick="printBarcode()">
                    </div>
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
            <h3>Datos Avanzados</h3>
            <?php if ($row): ?>
                <form action="" method="GET">
                    <!-- Campos de Datos Avanzados -->

                    <div class="form-group">
                        <label for="nom_procesador">Procesador:</label>
                        <input type="text" name="nom_procesador" value="<?= htmlspecialchars(trim($row['nom_procesador'])); ?>" readonly>
                        <label for="generacion">Generación del Procesador:</label>
                        <input type="text" name="generacion" value="<?= htmlspecialchars(trim($row['generacion'])); ?>" readonly>
                        <label for="velocidad">Velocidad del Procesador:</label>
                        <input type="text" name="velocidad" value="<?= htmlspecialchars(trim($row['velocidad'])); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="tipo">Tipo de Disco:</label>
                        <input type="text" name="tipo" value="<?= htmlspecialchars(trim($row['tipo'])); ?>" readonly>
                        <label for="entrada">Entrada del Disco:</label>
                        <input type="text" name="entrada" value="<?= htmlspecialchars(trim($row['entrada'])); ?>" readonly>
                        <label for="capacidad">Capacidad del Disco:</label>
                        <input type="text" name="capacidad" value="<?= htmlspecialchars(trim($row['capacidad'])); ?>" readonly>
                    </div>
                </form>
            <?php else: ?>
                <p>No se encontró el artículo solicitado.</p>
            <?php endif; ?>
        </div>
    </section>
</div>

<script>
    // Generar código de barras usando JsBarcode
    const barcodeValue = "<?= htmlspecialchars($row['codigodebarras'] ?? ''); ?>";
    if (barcodeValue) {
        JsBarcode("#barcode", barcodeValue, {
            format: "CODE128",
            width: 2,
            height: 50,
            displayValue: true
        });
    }

    // Función para imprimir solo el código de barras
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
