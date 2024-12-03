<?php
include 'topbar.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="Funciones.js" defer></script>
</head>
<body>
<?php include 'topbar.php'; ?>

<div class="sidebar" id="sidebar">
    <button class="openbtn" onclick="toggleMenu()">&#9776;  | Menú |</button>

    <br>
    <nav>
        <ul>
            <li><a href="Inicio.php"><i class="fas fa-home"></i> Inicio </a></li>

            <li>
                <a href="#" onclick="toggleSubMenu(event)"><i class="fas fa-barcode"></i>Almacén + </a>
                <ul class="sub-menu">
                    <li><a href="entradastep0.php"><i class="fas fa-inbox"></i> Entradas</a></li>
                    <li><a href="salidastep0.php"><i class="fas fa-outdent"></i> Salidas</a></li>
                    <li><a href="transferstep0.php"><i class="fas fa-inbox"></i> Transferencias</a></li>
                    <li><a href="MantenimientoAlmacen.php"><i class="fas fa-inbox"></i> Mantenimiento</a></li>
                </ul>
            </li>

            <li>
                <a href="#" onclick="toggleSubMenu(event)"><i class="fas fa-laptop"></i>Equipos + </a>
                <ul class="sub-menu">
                    <li><a href="Equipos.php"><i class="fas fa-laptop"></i> Gestión de equipos</a></li>
                    <li><a href="equiposclientes.php"><i class="fas fa-laptop"></i> Buscar equipos por cliente</a></li>
                </ul>
            </li>

            <li>
                <a href="#" onclick="toggleSubMenu(event)"><i class="fas fa-users"></i>Clientes + </a>
                <ul class="sub-menu">
                    <li><a href="Clientes.php"><i class="fas fa-laptop"></i> Gestión de clientes</a></li>
                    <li><a href="Clientes.php"><i class="fas fa-laptop"></i> Buscar clientes por equipo</a></li>
                </ul>
            </li>

            <li>
                <a href="#" onclick="toggleSubMenu(event)"><i class="fas fa-book"></i>Catálogos + </a>
                <ul class="sub-menu">
                    <li><a href="articulos.php"><i class="fas fa-book"></i> Artículos</a></li>
                    <li><a href="marcas.php"><i class="fas fa-tag"></i> Marcas</a></li>
                    <li><a href="modelos.php"><i class="fas fa-tags"></i> Modelos</a></li>
                    <li><a href="series.php"><i class="fas fa-barcode"></i> Series</a></li>
                    <li><a href="procesadores.php"><i class="fas fa-microchip"></i> Procesadores</a></li>
                    <li><a href="discos.php"><i class="fas fa-hdd"></i> Discos duros</a></li>
                    <li><a href="#"><i class="fas fa-memory"></i> Memorias</a></li>
                    <li><a href="#"><i class="fas fa-desktop"></i> Software</a></li>
                    <li><a href="#"><i class="fas fa-ruler-combined"></i> Dimensiones</a></li>
                </ul>
            </li>

            <li>
                <a href="reportes.php" onclick="toggleSubMenu(event)"><i class="fas fa-chart-bar"></i> Reportes + </a>
                <ul class="sub-menu">
                <div class="grid-item"><a href="reportes_ventayrenta.php"><i class="fas fa-chart-line"></i>  Reporte de Venta y Renta </a></div>
                <div class="grid-item"><a href="#"><i class="fas fa-sync-alt"></i>  Reporte de estatus de equipos</a></div>
                <div class="grid-item"><a href="#"><i class="fas fa-user-tag"></i >Transacciones por usuario </a></div>
                </ul>
            </li>

            <li><a href="accounts.php"><i class="fas fa-user"></i> Usuarios </a></li>
        </ul>
    </nav>
</div>

<script>
    function toggleSubMenu(event) {
        event.preventDefault(); // Previene el comportamiento predeterminado del enlace
        const subMenu = event.target.nextElementSibling;
        if (subMenu && subMenu.classList.contains('sub-menu')) {
            subMenu.classList.toggle('active'); // Cambia la clase del submenú
        }
    }
</script>

</body>
</html>
