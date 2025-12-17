<?php
session_start();
date_default_timezone_set('America/Lima');

require(__DIR__ . '/../vendor/fpdf/fpdf.php');
require(__DIR__ . '/../db.php');

// ===============================
// DATOS DEL USUARIO LOGUEADO
// ===============================
$usuario = $_SESSION['admin_name'] ?? 'Usuario';
$correo  = $_SESSION['admin_email'] ?? 'Correo no disponible';
// Sanitizar valores para evitar contenido inesperado en el PDF
$usuario = strip_tags($usuario);
$usuario = preg_replace('/[\x00-\x1F\x7F]/', '', $usuario);
$correo = strip_tags($correo);
$correo = preg_replace('/[\x00-\x1F\x7F]/', '', $correo);
$fecha   = date('d/m/Y H:i:s');

// ===============================
// CREAR PDF
// ===============================
$pdf = new FPDF('L'); // Horizontal para que entre todo
$pdf->AddPage();

// ===============================
// LOGO
// ===============================
$pdf->Image(__DIR__ . '/../img/logo_cersa.png', 120, 10, 40);
$pdf->Ln(25);

// ===============================
// TÍTULO
// ===============================
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, utf8_decode('LISTADO GENERAL DE CURSOS'), 0, 1, 'C');
$pdf->Ln(5);

// ===============================
// INFO DEL REPORTE
// ===============================
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, utf8_decode("Generado por: $usuario"), 0, 1);
$pdf->Cell(0, 8, utf8_decode("Correo: $correo"), 0, 1);
$pdf->Cell(0, 8, utf8_decode("Fecha y hora: $fecha"), 0, 1);
$pdf->Ln(6);

// ===============================
// CABECERA DE TABLA (GRIS)
// ===============================
$pdf->SetFillColor(230, 230, 230);
$pdf->SetFont('Arial', 'B', 10);

$pdf->Cell(60, 8, 'Curso', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'Categoria', 1, 0, 'C', true);
$pdf->Cell(35, 8, 'Modalidad', 1, 0, 'C', true);
$pdf->Cell(45, 8, 'Docente', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'Fecha Inicio', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'Duración', 1, 0, 'C', true);
$pdf->Cell(20, 8, 'Cupos', 1, 1, 'C', true);

// ===============================
// DATOS
// ===============================
$pdf->SetFont('Arial', '', 10);

$sql = "
    SELECT 
        c.nombre AS curso,
        cat.nombre AS categoria,
        m.nombre AS modalidad,
        d.nombre AS docente,
        c.fecha_inicio,
        c.duracion,
        c.cupos
    FROM curso c
    JOIN categoria cat ON c.categoria_id = cat.id
    JOIN modalidad m ON c.modalidad_id = m.id
    JOIN docente d ON c.docente_id = d.id
    ORDER BY c.nombre ASC
";

$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $curso = strip_tags($row['curso']);
    $curso = preg_replace('/[\x00-\x1F\x7F]/', '', $curso);
    $categoria = strip_tags($row['categoria']);
    $categoria = preg_replace('/[\x00-\x1F\x7F]/', '', $categoria);
    $modalidad = strip_tags($row['modalidad']);
    $modalidad = preg_replace('/[\x00-\x1F\x7F]/', '', $modalidad);
    $docente = strip_tags($row['docente']);
    $docente = preg_replace('/[\x00-\x1F\x7F]/', '', $docente);

    $pdf->Cell(60, 8, utf8_decode($curso), 1);
    $pdf->Cell(40, 8, utf8_decode($categoria), 1);
    $pdf->Cell(35, 8, utf8_decode($modalidad), 1);
    $pdf->Cell(45, 8, utf8_decode($docente), 1);
    $pdf->Cell(30, 8, $row['fecha_inicio'], 1, 0, 'C');
    $pdf->Cell(25, 8, $row['duracion'] . ' h', 1, 0, 'C');
    $pdf->Cell(20, 8, (int)$row['cupos'], 1, 1, 'C');
}

// ===============================
// SALIDA
// ===============================
$pdf->Output('I', 'Reporte_Cursos.pdf');
exit;
