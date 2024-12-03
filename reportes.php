<?php  
include 'connection.php';
require_once 'ValidaSesion.php';

// Consulta para obtener la cantidad de equipos asignados por cliente
$query = "SELECT cliente, COUNT(*) AS cantidad_equipos FROM clientesequipos GROUP BY cliente";
$result = mysqli_query($conn, $query);

$clientes = [];
$cantidades = [];

while ($row = mysqli_fetch_assoc($result)) {
    $clientes[] = $row['cliente'];
    $cantidades[] = $row['cantidad_equipos'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Equipos por Cliente</title>
    
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js -->
</head>
<body>
    <?php include 'menu.php'; ?>
    <script src="menujs.js"></script>

    <div class="main-content" id="main-content">
        <button class="openbtn" onclick="toggleMenu()">&#9776;</button>

        <!-- Sección del reporte de equipos por cliente -->
        <div class="report-section">
            <h2>Reporte de Equipos Asignados por Cliente</h2>
            <canvas id="equiposChart" width="400" height="200"></canvas>
        </div>
    </div>

    <script>
        // Datos de PHP convertidos a JSON para usarlos en Chart.js
        const clientes = <?php echo json_encode($clientes); ?>;
        const cantidades = <?php echo json_encode($cantidades); ?>;

        const ctx = document.getElementById('equiposChart').getContext('2d');
        const equiposChart = new Chart(ctx, {
            type: 'bar', // Gráfico de barras
            data: {
                labels: clientes,
                datasets: [{
                    label: 'Cantidad de equipos asignados',
                    data: cantidades,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)', // Color de las barras
                    borderColor: 'rgba(54, 162, 235, 1)', // Borde de las barras
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true // Comenzar en 0 el eje Y
                    }
                }
            }
        });
    </script>
</body>
</html>
