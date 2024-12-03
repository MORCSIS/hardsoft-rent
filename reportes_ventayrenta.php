<?php  
include 'connection.php';
require_once 'ValidaSesion.php';
$conn = connection();

// Consulta SQL para la cantidad de codigodebarras por estatus
$query = "SELECT ce.id_estatus, COUNT(codigodebarras) AS cantidad_codigobarras, es.nom_estatus 
          FROM clientesequipos ce
          LEFT JOIN estatus es ON ce.id_estatus = es.id_estatus
          GROUP BY ce.id_estatus";
$result = mysqli_query($conn, $query);

$estatus = [];
$cantidades = [];

// Verifica si hay resultados y los almacena en arreglos
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $estatus[] = "Estatus " . $row['nom_estatus'];
        $cantidades[] = intval($row['cantidad_codigobarras']); // Convertir explícitamente a entero
    }
} else {
    echo "<p>No hay resultados para estatus.</p>";
}

// Consulta SQL para la cantidad de codigodebarras por id_cliente
$query_clientes = "SELECT id_cliente, COUNT(codigodebarras) AS cantidad_codigobarras 
                   FROM clientesequipos 
                   GROUP BY id_cliente";
$result_clientes = mysqli_query($conn, $query_clientes);

$clientes = [];
$cantidades_clientes = [];

// Verifica si hay resultados y los almacena en arreglos
if (mysqli_num_rows($result_clientes) > 0) {
    while ($row = mysqli_fetch_assoc($result_clientes)) {
        $clientes[] = "Cliente " . $row['id_cliente']; // Personaliza el nombre si es necesario
        $cantidades_clientes[] = intval($row['cantidad_codigobarras']);
    }
} else {
    echo "<p>No hay resultados para clientes.</p>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Códigos de Barras</title>
    
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .report-section {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin: 20px;
            width: 80%;
            max-width: 800px;
            display: inline-block; /* Permitir que ambos gráficos estén uno al lado del otro */
        }
        #equiposChart, #clientesChart {
            width: 100% !important;
            height: 400px !important;
        }
        .charts-container {
            display: flex; /* Usar flexbox para alinear los gráficos horizontalmente */
            justify-content: space-between; /* Espacio entre los gráficos */
        }
    </style>
</head>
<body>
    <?php include 'menu.php'; ?>
    <script src="menujs.js"></script>

    <div class="main-content" id="main-content">
        <button class="openbtn" onclick="toggleMenu()">&#9776;</button>
        <h2>Reportes de Códigos de Barras</h2>

        <div class="charts-container">
            <div class="report-section">
                <h3>Reporte de Cantidad de Códigos de Barras por Estatus</h3>
                <canvas id="equiposChart"></canvas>
            </div>

            <div class="report-section">
                <h3>Reporte de Porcentaje de Códigos de Barras por Cliente</h3>
                <canvas id="clientesChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Depuración en JavaScript
        console.log('Estatus:', <?php echo json_encode($estatus); ?>);
        console.log('Cantidades:', <?php echo json_encode($cantidades); ?>);
        console.log('Clientes:', <?php echo json_encode($clientes); ?>);
        console.log('Cantidades Clientes:', <?php echo json_encode($cantidades_clientes); ?>);

        // Datos del gráfico de barras
        const estatus = <?php echo json_encode($estatus); ?>;
        const cantidades = <?php echo json_encode($cantidades); ?>;

        const ctxBarras = document.getElementById('equiposChart').getContext('2d');
        const equiposChart = new Chart(ctxBarras, {
            type: 'bar',
            data: {
                labels: estatus,
                datasets: [{
                    label: 'Cantidad de Códigos de Barras',
                    data: cantidades,
                    backgroundColor: 'rgba(54, 162, 235, 0.8)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0  // Asegura que los números sean enteros
                        }
                    }
                }
            }
        });

        // Datos del gráfico de pastel
        const clientes = <?php echo json_encode($clientes); ?>;
        const cantidadesClientes = <?php echo json_encode($cantidades_clientes); ?>;

        const ctxPastel = document.getElementById('clientesChart').getContext('2d');
        const clientesChart = new Chart(ctxPastel, {
            type: 'pie', // Gráfico de pastel
            data: {
                labels: clientes,
                datasets: [{
                    label: 'Porcentaje de Códigos de Barras por Cliente',
                    data: cantidadesClientes,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(255, 159, 64, 0.8)',
                        'rgba(255, 205, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(153, 102, 255, 0.8)'
                    ],
                    borderColor: 'rgba(255, 255, 255, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Porcentaje de Códigos de Barras por Cliente'
                    }
                }
            }
        });
    </script>
</body>
</html>
