<?php
include 'connection.php';
require_once 'ValidaSesion.php';
require_once 'ValidaSesion.php';

require 'fpdf.php';
$con = connection();
$con->set_charset("utf8");

$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';

// Formato de fecha actual
$filename = 'equipos_' . date('YmdHis') . '.pdf';

class PDF extends FPDF {
    // Cabecera del PDF
    function Header() {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Lista de Equipos', 0, 1, 'C');
    }

    // Pie de página
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 10);

// Agregar los encabezados de la tabla
$pdf->Cell(20, 10, 'ID Material', 1);
$pdf->Cell(30, 10, 'Codigo Barras', 1);
$pdf->Cell(30, 10, 'ID Articulo', 1);
$pdf->Cell(40, 10, 'Nombre Articulo', 1);
$pdf->Cell(30, 10, 'ID Marca', 1);
$pdf->Cell(40, 10, 'Nombre Marca', 1);
$pdf->Ln();

// Realizar la consulta y agregar los datos al PDF
$sql = "SELECT DISTINCT M.id_material, M.codigodebarras, M.id_articulo, M.id_marca, M.id_modelo, M.id_serie, M.id_estatus, AR.nom_articulo, MA.nom_marca, MO.nom_modelo, SE.nom_serie, ES.nom_estatus FROM materiales M LEFT JOIN articulos AR ON M.id_articulo = AR.id_articulo LEFT JOIN marcas MA ON M.id_marca = MA.id_marca LEFT JOIN modelos MO ON M.id_modelo = MO.id_modelo LEFT JOIN series SE ON M.id_serie = SE.id_serie LEFT JOIN estatus ES ON M.id_estatus = ES.id_estatus WHERE M.codigodebarras = '$busqueda' OR AR.nom_articulo LIKE '%$busqueda%' OR MA.nom_marca LIKE '%$busqueda%' OR MO.nom_modelo LIKE '%$busqueda%' OR SE.nom_serie LIKE '%$busqueda%' OR ES.nom_estatus LIKE '%$busqueda%'";
$result = $con->query($sql);

while ($row = $result->fetch_assoc()) {
    $pdf->Cell(20, 10, $row['id_material'], 1);
    $pdf->Cell(30, 10, $row['codigodebarras'], 1);
    $pdf->Cell(30, 10, $row['id_articulo'], 1);
    $pdf->Cell(40, 10, $row['nom_articulo'], 1);
    $pdf->Cell(30, 10, $row['id_marca'], 1);
    $pdf->Cell(40, 10, $row['nom_marca'], 1);
    $pdf->Ln();
}

$pdf->Output('D', $filename);
exit;
?>
