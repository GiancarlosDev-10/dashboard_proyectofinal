<?php
require(__DIR__ . '/../vendor/fpdf/fpdf.php');
require(__DIR__ . '/../db.php');

// Crear PDF
$pdf = new FPDF();
$pdf->AddPage();

// Título
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, utf8_decode('Reporte General de Alumnos'), 0, 1, 'C');
$pdf->Ln(5);

// Subtítulo
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, 'Listado de alumnos registrados', 0, 1);

// Tabla
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20, 8, 'ID', 1);
$pdf->Cell(70, 8, 'Nombre', 1);
$pdf->Cell(40, 8, 'DNI', 1);
$pdf->Cell(60, 8, 'Email', 1);
$pdf->Ln();

// Datos
$pdf->SetFont('Arial', '', 10);

$result = $conn->query("SELECT id, nombre, dni, email FROM alumno ORDER BY id ASC");

while ($row = $result->fetch_assoc()) {
    $pdf->Cell(20, 8, $row['id'], 1);
    $pdf->Cell(70, 8, utf8_decode($row['nombre']), 1);
    $pdf->Cell(40, 8, $row['dni'], 1);
    $pdf->Cell(60, 8, $row['email'], 1);
    $pdf->Ln();
}

$pdf->Output('I', 'Reporte_Alumnos.pdf');
exit;
