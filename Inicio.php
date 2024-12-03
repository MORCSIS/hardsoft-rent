<?php  
include 'connection.php';
require_once 'ValidaSesion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HardSoft Rent, SA de CV - Gestión de Inventarios</title>

    <style>
        /* Estilo para el fondo de la página */
        body {
            margin: 0;
            padding: 0;
            height: 100%; /* Asegura que cubra toda la pantalla */
            background-image: url('black-1072366_1280.jpg'); /* Ruta de la imagen */
            background-size: cover; /* Escalar la imagen para cubrir la pantalla */
            background-position: center; /* Centrar la imagen */
            background-repeat: no-repeat; /* Evita que la imagen se repita */
        }
    </style>
    
    <link rel="stylesheet" href="styles.css"> 
</head>
<body>
    <?php include 'menu.php'; ?>
    <script src="menujs.js"></script>

    <div class="main-content" id="main-content">
        <button class="openbtn" onclick="toggleMenu()">&#9776;</button>

        <!-- Cuadro de bienvenida -->
            <div class="welcome-box">
                <h1>Bienvenido a HardSoft Rent, SA de CV</h1>
                <p>Sistema Interno para la gestión de inventarios.</p>
            </div>
            <div class="logo">
                <img src="logo.png" alt="Logotipo de HardSoft Rent" class="logo">
            </div>
    </div>
</body>
</html>

