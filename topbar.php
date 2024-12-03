<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="estilos.css"> <!-- Asegúrate de tener este enlace -->
    <title>Topbar</title>
</head>
<body>
    <div class="topbar">
    <div class="date-time">
        <span><?php echo date('l, j \d\e F \d\e Y - g:i a'); ?></span>
    </div>

    <div class="user-info">
            <div class="avatar" onclick="toggleDropdown()">
                <span>
                    <?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?>
                </span>
            </div>
            <div class="username">
                <span><?php echo htmlspecialchars($_SESSION['name']); ?></span>
            </div>
            <div class="dropdown-content" id="dropdownMenu">
                <a href="perfil.php">Perfil</a>
                <a href="passchange.php">Cambiar contraseña</a>
                <a href="cerrar-sesion.php">Cerrar sesión</a>
                
            </div>
        </div>
    </div>

    <script>
        function toggleDropdown() {
            document.getElementById('dropdownMenu').classList.toggle('show');
        }
        
        // Cierra el menú desplegable si el usuario hace clic fuera de él
        window.onclick = function(event) {
            if (!event.target.matches('.avatar')) {
                const dropdowns = document.getElementsByClassName('dropdown-content');
                for (let i = 0; i < dropdowns.length; i++) {
                    const openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>
</body>
</html>
