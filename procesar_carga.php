<?php

// Incluir librerías necesarias para leer Excel
require 'vendor/autoload.php'; // Asegúrate de tener PhpSpreadsheet instalado vía Composer
use PhpOffice\PhpSpreadsheet\IOFactory;

// Conexión a la base de datos
$conexion = new PDO("mysql:host=localhost;dbname=tu_base_datos", "usuario", "password");
$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function validarNombreArticulo($nombre) {
    // Solo permite letras, números y espacios
    return preg_match('/^[a-zA-Z0-9\s]+$/', $nombre);
}

try {
    if ($_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
        $nombreArchivo = $_FILES['archivo']['tmp_name'];
        $extension = strtolower(pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION));
        
        // Validar extensión
        if (!in_array($extension, ['xlsx', 'xls', 'csv'])) {
            throw new Exception('Formato de archivo no válido. Solo se permiten archivos XLSX, XLS o CSV.');
        }

        // Leer el archivo
        $spreadsheet = IOFactory::load($nombreArchivo);
        $worksheet = $spreadsheet->getActiveSheet();
        $articulos = [];
        
        // Obtener datos del archivo
        foreach ($worksheet->getRowIterator(2) as $row) { // Empezar desde la fila 2 si hay encabezados
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            
            foreach ($cellIterator as $cell) {
                $nombreArticulo = trim($cell->getValue());
                if (!empty($nombreArticulo)) {
                    // Validar formato del nombre
                    if (!validarNombreArticulo($nombreArticulo)) {
                        throw new Exception("El artículo '$nombreArticulo' contiene caracteres no permitidos.");
                    }
                    $articulos[] = $nombreArticulo;
                }
            }
        }

        // Verificar duplicados en el archivo
        $duplicadosArchivo = array_diff_assoc($articulos, array_unique($articulos));
        if (!empty($duplicadosArchivo)) {
            throw new Exception('El archivo contiene artículos duplicados.');
        }

        // Verificar existentes en la base de datos
        $placeholders = str_repeat('?,', count($articulos) - 1) . '?';
        $stmt = $conexion->prepare("SELECT nom_articulo FROM articulos WHERE nom_articulo IN ($placeholders)");
        $stmt->execute($articulos);
        $existentes = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (!empty($existentes)) {
            throw new Exception('Se encontraron ' . count($existentes) . ' artículos que ya existen en la base de datos.');
        }

        // Insertar nuevos artículos
        $stmt = $conexion->prepare("INSERT INTO articulos (nom_articulo) VALUES (?)");
        $registrosInsertados = 0;

        foreach ($articulos as $articulo) {
            $stmt->execute([$articulo]);
            $registrosInsertados++;
        }

        // Respuesta exitosa
        echo json_encode([
            'status' => 'success',
            'message' => "Se han insertado exitosamente $registrosInsertados artículos.",
            'registros' => $registrosInsertados
        ]);

    } else {
        throw new Exception('Error al subir el archivo.');
    }

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

?>