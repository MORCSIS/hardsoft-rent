<?php 
include 'connection.php';
require_once 'ValidaSesion.php';

// Función para validar el nombre y extensión del archivo
function validateFileName($fileName) {
    $expectedName = 'marcas.csv';
    return strtolower($fileName) === $expectedName;
}

// Función para validar extensión de archivo
function validateFileExtension($fileName) {
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    return $fileExtension === 'csv';
}

// Función para leer archivo CSV
function readCSVFile($filePath) {
    $data = [];
    if (($handle = fopen($filePath, "r")) !== FALSE) {
        while (($row = fgetcsv($handle)) !== FALSE) {
            $row = array_map('trim', $row);
            $row = array_map(function($value) {
                return mb_convert_encoding($value, 'UTF-8', 'auto');
            }, $row);
            
            if (count($data) > 0) {
                $row[1] = mb_strtoupper($row[1], 'UTF-8');
            }
            
            $data[] = $row;
        }
        fclose($handle);
    }
    return $data;
}

// Función para validar datos
function validateData($data) {
    $errors = [];
    $seen = [];
    
    foreach ($data as $index => $row) {
        if ($index === 0) continue;
        
        if (count($row) < 2) {
            $errors[] = "La fila " . ($index + 1) . " no tiene todas las columnas requeridas.";
            continue;
        }

        if (!is_numeric($row[0])) {
            $errors[] = "El ID en la fila " . ($index + 1) . " no es numérico.";
        }

        $key = $row[0] . '-' . $row[1];
        if (isset($seen[$key])) {
            $errors[] = "Registro duplicado encontrado: ID " . $row[0] . ", Nombre: " . $row[1];
        }
        $seen[$key] = true;
    }
    
    return $errors;
}

// Función para verificar registros existentes
function checkExistingRecords($data, $con) {
    $existing = [];
    
    foreach ($data as $index => $row) {
        if ($index === 0) continue;
        
        $stmt = $con->prepare("SELECT id_marca FROM marcas WHERE id_marca = ? OR UPPER(nom_marca) = ?");
        $stmt->bind_param("is", $row[0], $row[1]);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $existing[] = "ID: " . $row[0] . ", Nombre: " . $row[1];
        }
    }
    
    return $existing;
}

// Procesamiento de formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['marcas'])) {
    $file = $_FILES['marcas'];
    $response = [];
    
    if (!validateFileName($file['name'])) {
        $response = ['error' => 'El archivo debe llamarse "marcas.csv" para continuar.'];
    } elseif (!validateFileExtension($file['name'])) {
        $response = ['error' => 'Formato de archivo no válido. Solo se permite CSV.'];
    } else {
        $data = readCSVFile($file['tmp_name']);
        
        $validationErrors = validateData($data);
        if (!empty($validationErrors)) {
            $response = ['error' => 'Errores en el archivo:', 'details' => $validationErrors];
        } else {
            $con = connection();
            $existingRecords = checkExistingRecords($data, $con);
            
            if (!empty($existingRecords)) {
                $response = ['error' => 'Registros existentes encontrados:', 'details' => $existingRecords];
            } else {
                $recordCount = count($data) - 1;
                $response = ['success' => true, 'count' => $recordCount];
                
                if (isset($_POST['confirm']) && $_POST['confirm'] === 'true') {
                    $stmt = $con->prepare("INSERT INTO marcas (id_marca, nom_marca) VALUES (?, ?)");
                    $insertedCount = 0;
                    
                    foreach ($data as $index => $row) {
                        if ($index === 0) continue;
                        $stmt->bind_param("is", $row[0], $row[1]);
                        if ($stmt->execute()) {
                            $insertedCount++;
                        }
                    }
                    
                    header("Location: marcas.php?success=true&count=" . $insertedCount);
                    exit;
                }
            }
        }
    }
    
    echo json_encode($response);
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carga Masiva de Marcas</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'menu.php'; ?>
    <script src="menujs.js"></script>

    <div class="main-content" id="main-content">
        <button class="openbtn" onclick="toggleMenu()">&#9776;</button>
        
        <div class="content-h2">
            <h2>Carga Masiva de Marcas</h2>
        </div>
        <section class="content-grid">
        <div class="form-container">
        <form id="uploadForm" method="POST" enctype="multipart/form-data">
                <div>
                    <label for="marcas">Seleccione archivo CSV:</label>
                    <input type="file" name="marcas" id="marcas" accept=".csv" required>
                </div>

                <div class="button-group">
                    <button type="button" onclick="window.location.href='marcas.php'">Cancelar</button>
                    <button type="submit" id="uploadButton">Cargar</button>
                </div>
            </form>
        <div class="upload-container">
        <div id="messageContainer"></div>
            <div class="file-info">
                <h4>Instrucciones:</h4>
                <p>1. El archivo debe estar en formato CSV</p>
                <p>2. La primera columna debe contener el ID de la marca (numérico)</p>
                <p>3. Considera el último ID de marca, deben ser contínuos </p>
                <p>4. La segunda columna debe contener el nombre de la marca</p>
                <p>5. Se recomienda incluir una fila de encabezados</p>
                <p>6. Ejemplo de formato:</p>
                <pre>id_marca,nom_marca
1,Marca 1
2,Marca 2</pre>
            </div>

        </div>
    </div>
</div>
</section>

    <script>
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const messageContainer = document.getElementById('messageContainer');
            const uploadButton = document.getElementById('uploadButton');
            
            uploadButton.disabled = true;
            messageContainer.innerHTML = '<div class="info-message">Procesando archivo...</div>';
            
            fetch('marcas_carga_masiva.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                uploadButton.disabled = false;
                
                if (data.error) {
                    let errorMessage = `<div class="error-message"><p>${data.error}</p>`;
                    if (data.details) {
                        errorMessage += '<ul>';
                        data.details.forEach(detail => {
                            errorMessage += `<li>${detail}</li>`;
                        });
                        errorMessage += '</ul>';
                    }
                    errorMessage += '</div>';
                    messageContainer.innerHTML = errorMessage;
                } else if (data.success) {
                    if (confirm(`Se van a cargar ${data.count} registros. ¿Está de acuerdo?`)) {
                        const confirmFormData = new FormData(document.getElementById('uploadForm'));
                        confirmFormData.append('confirm', 'true');
                        
                        uploadButton.disabled = true;
                        messageContainer.innerHTML = '<div class="info-message">Cargando registros...</div>';

                        <?php 
                        //Ejecuta la funcion registerAction que se encuentra en el archivo ValidaSesion
                        $id_usuario = $_SESSION['id'];
                        $accion = "Creación"; // Reemplaza con el tipo de acción (Creación, Actualización, Eliminación, Consulta, etc.)
                        $descripcion = "Creación realizada en la página " . $currentPage;
                        registerAction($id_usuario, $accion, $descripcion);
                        ?>

                        fetch('marcas_carga_masiva.php', {
                            method: 'POST',
                            body: confirmFormData
                        })
                        .then(response => {
                            window.location.href = `marcas.php?success=true&count=${data.count}`;
                        })
                        .catch(error => {
                            uploadButton.disabled = false;
                            messageContainer.innerHTML = `<div class="error-message">Error al procesar la carga: ${error}</div>`;
                        });
                    }
                }
            })
            .catch(error => {
                uploadButton.disabled = false;
                messageContainer.innerHTML = `<div class="error-message">Error al procesar el archivo: ${error}</div>`;
            });
        });
    </script>
</body>
</html>
