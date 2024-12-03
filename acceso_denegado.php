<?php 
require_once 'ValidaSesion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Acceso denegado </title>
    <link rel="stylesheet" href="styles.css">
</head>

    <body>
    <div class="main-content" id="main-content">
    <h2>Acceso denegado</h2>
    </div>
    </body>

    
    </html>
    
    <script>
        function mostrarAlerta() {
            alert("Usted no tiene permisos necesarios, contacte al administrador.");
            window.history.back(); // Regresa a la página anterior
        }
    </script>

    <script>  mostrarAlerta(); </script>