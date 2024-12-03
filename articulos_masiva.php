<?php
include 'connection.php';
require_once 'ValidaSesion.php';
$con = connection();

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
    
    <form action="procesar_carga.php" method="POST" enctype="multipart/form-data">
    <h3>Carga masiva: artículos</h3>
                    <label>Seleccione el archivo (XLSX, XLS o CSV):</label><br>
                    <input type="file" name="archivo" accept=".xlsx,.xls,.csv" required>
                <input type="submit" class="btn" value="Procesar archivo">
            </form>
    </div>
</section>   
</div>
</body>
</html>

