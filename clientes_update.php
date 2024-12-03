<?php
include 'connection.php';
require_once 'ValidaSesion.php';
$con = connection();
$busqueda = isset($_GET['id_cliente']) ? $_GET['id_cliente'] : '';

// Obtener los registros para el detalle del cliente
$sql = "SELECT * FROM clientes WHERE id_cliente = $busqueda";

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
    <title>Gestión de Clientes: Editar Cliente</title>
</head>

<body>
    <?php include 'menu.php'; ?>
    <script src="menujs.js"></script>

    <div class="main-content" id="main-content">
        <button class="openbtn" onclick="toggleMenu()">&#9776;</button>

        <h2>Gestión de Clientes: Actualizar Cliente</h2>
        <section class="content-grid">
            <div class="form-container">
                <h3>Datos actuales del cliente</h3>
                <?php if ($row): ?>
                    <form action="" method="GET">
                        <div class="form-group">
                            <label for="id_cliente">ID Cliente:</label>
                            <input type="text" name="id_cliente" value="<?= htmlspecialchars(trim($row['id_cliente'])); ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label for="nombre_cliente">Nombre del Cliente:</label>
                            <input type="text" name="nombre_cliente" value="<?= htmlspecialchars(trim($row['nombre_cliente'])); ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label for="cliente_rfc">RFC:</label>
                            <input type="text" name="cliente_rfc" value="<?= htmlspecialchars(trim($row['cliente_rfc'])); ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label for="cliente_cp">Código Postal:</label>
                            <input type="text" name="cliente_cp" value="<?= htmlspecialchars(trim($row['cliente_cp'])); ?>" readonly>
                        </div>
                    </form>
                <?php else: ?>
                    <p>No se encontró el cliente solicitado.</p>
                <?php endif; ?>
            </div>

            <div class="form-container">
                <h3>Datos nuevos para el cliente</h3>
                <form action="clientes_edit.php" method="POST">
                    <input type="hidden" name="id_cliente" value="<?= htmlspecialchars($busqueda); ?>">

                    <div class="form-group">
                        <label for="id_empresa">Empresa:</label>
                        <select name="id_empresa" required>
                            <?php
                            $sqlempresas = "SELECT * FROM empresas ORDER BY id_empresa ASC";
                            $queryempresas = mysqli_query($con, $sqlempresas);
                            echo '<option value="" disabled>Selecciona una opción</option>';
                            while ($listaempresas = mysqli_fetch_array($queryempresas)) {
                                $selected = ($listaempresas['id_empresa'] == $row['id_empresa']) ? 'selected' : '';
                                echo '<option value="' . $listaempresas['id_empresa'] . '" ' . $selected . '>' . $listaempresas['id_empresa'] . ' - ' . $listaempresas['nom_empresa'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="nombre_cliente">Nombre del Cliente:</label>
                        <input type="text" name="nombre_cliente" value="<?= htmlspecialchars(trim($row['nombre_cliente'])); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="cliente_rfc">RFC:</label>
                        <input type="text" name="cliente_rfc" value="<?= htmlspecialchars(trim($row['cliente_rfc'])); ?>" 
                               pattern="[A-Za-z0-9]{13}" maxlength="13" title="El RFC debe contener exactamente 13 caracteres alfanuméricos." required>
                    </div>

                    <div class="form-group">
                        <label for="cliente_cp">Código Postal:</label>
                        <input type="text" name="cliente_cp" value="<?= htmlspecialchars(trim($row['cliente_cp'])); ?>" 
                               pattern="[0-9]{5}" maxlength="5" title="El Código Postal debe contener exactamente 5 dígitos." required>
                    </div>

                    <div class="button-container">
                        <input type="submit" value="Actualizar">
                        <input type="button" value="Cancelar" onClick="location.href='Clientes.php'">
                    </div>
                </form>
            </div>
        </section>
    </div>
</body>
</html>
