<?php

require_once("../db.php");

// Ruta a la librería FPDF (ajusta si tu ruta es distinta)
$fpdfPath = __DIR__ . '/../vendor/fpdf/fpdf.php';
if (!file_exists($fpdfPath)) {
    die("Error: la librería FPDF no fue encontrada en {$fpdfPath}. Verifica la ruta.");
}
require_once $fpdfPath;

// Obtener ID del alumno
$alumnoId = intval($_GET['alumno_id'] ?? 0);
if ($alumnoId <= 0) {
    die("Error: ID de alumno no válido.");
}

// ============================================
// OBTENER DATOS DEL ALUMNO
// ============================================
$sqlAlumno = "SELECT nombre, dni, email, celular FROM alumno WHERE id = ?";
$stmtAlumno = $conn->prepare($sqlAlumno);
if (!$stmtAlumno) {
    die("Error en la consulta del alumno: " . $conn->error);
}
$stmtAlumno->bind_param("i", $alumnoId);
$stmtAlumno->execute();
$resultAlumno = $stmtAlumno->get_result();

if ($resultAlumno->num_rows === 0) {
    $stmtAlumno->close();
    die("Error: Alumno no encontrado.");
}

$alumno = $resultAlumno->fetch_assoc();
// Sanitizar datos del alumno para evitar inyección o caracteres no deseados
$alumno['nombre'] = strip_tags($alumno['nombre']);
$alumno['nombre'] = preg_replace('/[\x00-\x1F\x7F]/', '', $alumno['nombre']);
$alumno['dni'] = preg_replace('/[^0-9A-Za-z\-]/', '', $alumno['dni']);
$alumno['email'] = strip_tags($alumno['email']);
$alumno['email'] = preg_replace('/[\x00-\x1F\x7F]/', '', $alumno['email']);
$alumno['celular'] = preg_replace('/[^0-9\+\- ]/', '', $alumno['celular']);
$stmtAlumno->close();

// ============================================
// OBTENER CURSOS MATRICULADOS
// ============================================
$sqlCursos = "SELECT c.nombre, c.precio, m.nombre as modalidad, c.duracion
            FROM matricula mat
            INNER JOIN curso c ON mat.curso_id = c.id
            INNER JOIN modalidad m ON c.modalidad_id = m.id
            WHERE mat.alumno_id = ?";

$stmtCursos = $conn->prepare($sqlCursos);
if (!$stmtCursos) {
    die("Error en la consulta de cursos: " . $conn->error);
}
$stmtCursos->bind_param("i", $alumnoId);
$stmtCursos->execute();
$resultCursos = $stmtCursos->get_result();

$cursos = [];
$totalGeneral = 0.0;

while ($row = $resultCursos->fetch_assoc()) {
    $cursos[] = $row;
    $totalGeneral += (float)$row['precio'];
}

$stmtCursos->close();
$conn->close();

if (empty($cursos)) {
    die("Error: El alumno no tiene cursos matriculados.");
}

// ============================================
// CREAR PDF (FPDF)
// ============================================
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(true, 15);
$pdf->AddPage();

// FPDF core no soporta UTF-8 por defecto; usamos utf8_decode() para acentos.
// Si usas una versión con soporte UTF-8 o agregas fuentes TTF, elimina utf8_decode().

// METADATOS
$pdf->SetTitle(utf8_decode('Ticket de Pago - ' . $alumno['nombre']));
$pdf->SetAuthor(utf8_decode('CERSA - I.E.S.T.P Gilda Ballivián Rosado'));

// HEADER
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 8, utf8_decode('CERSA'), 0, 1, 'C');

$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 5, utf8_decode('I.E.S.T.P "Gilda Liliana Ballivián Rosado"'), 0, 1, 'C');
$pdf->Cell(0, 5, utf8_decode('Soporte Técnico e Informático'), 0, 1, 'C');

$pdf->Ln(5);

// Línea separadora
$pdf->SetDrawColor(0, 0, 0);
$pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
$pdf->Ln(5);

// TÍTULO
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 8, utf8_decode('COMPROBANTE DE PAGO'), 0, 1, 'C');
$pdf->Ln(3);

// Información del ticket
$pdf->SetFont('Arial', '', 9);
$ticketNumero = 'TICKET-' . str_pad($alumnoId, 5, '0', STR_PAD_LEFT);
$fecha = date('Y-m-d H:i:s');

$pdf->Cell(90, 5, 'Ticket Nro: ' . $ticketNumero, 0, 0, 'L');
$pdf->Cell(0, 5, 'Fecha: ' . $fecha, 0, 1, 'R');
$pdf->Ln(2);

// Línea separadora
$pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
$pdf->Ln(5);

// DATOS DEL ALUMNO
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 6, utf8_decode('DATOS DEL ALUMNO:'), 0, 1, 'L');

$pdf->SetFont('Arial', '', 9);
$pdf->Cell(30, 5, utf8_decode('Nombre:'), 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 5, utf8_decode($alumno['nombre']), 0, 1, 'L');

$pdf->SetFont('Arial', '', 9);
$pdf->Cell(30, 5, utf8_decode('DNI:'), 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 5, utf8_decode($alumno['dni']), 0, 1, 'L');

$pdf->SetFont('Arial', '', 9);
$pdf->Cell(30, 5, utf8_decode('Email:'), 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 5, utf8_decode($alumno['email']), 0, 1, 'L');

$pdf->Ln(5);

// Línea separadora
$pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
$pdf->Ln(5);

// DETALLE DE CURSOS
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 6, utf8_decode('DETALLE DE CURSOS:'), 0, 1, 'L');
$pdf->Ln(2);

// Tabla de cursos (cabecera)
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(90, 6, utf8_decode('Curso'), 1, 0, 'L', true);
$pdf->Cell(50, 6, utf8_decode('Modalidad'), 1, 0, 'C', true);
$pdf->Cell(40, 6, utf8_decode('Precio'), 1, 1, 'R', true);

// Filas de cursos
$pdf->SetFont('Arial', '', 9);
foreach ($cursos as $curso) {
    $cursoNombre = strip_tags($curso['nombre']);
    $cursoNombre = preg_replace('/[\x00-\x1F\x7F]/', '', $cursoNombre);
    $modalidad = strip_tags($curso['modalidad']);
    $modalidad = preg_replace('/[\x00-\x1F\x7F]/', '', $modalidad);

    $pdf->Cell(90, 6, utf8_decode($cursoNombre), 1, 0, 'L');
    $pdf->Cell(50, 6, utf8_decode($modalidad), 1, 0, 'C');
    $pdf->Cell(40, 6, 'S/. ' . number_format((float)$curso['precio'], 2), 1, 1, 'R');
}
$pdf->Ln(3);

// TOTAL
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(220, 240, 220);
$pdf->Cell(140, 8, utf8_decode('TOTAL A PAGAR:'), 1, 0, 'R', true);
$pdf->Cell(40, 8, 'S/. ' . number_format($totalGeneral, 2), 1, 1, 'R', true);
$pdf->Ln(8);

// Línea separadora
$pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
$pdf->Ln(5);

// CÓDIGO QR (generado por Google Charts API como PNG temporal)
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 5, utf8_decode('Escanea para verificar el ticket:'), 0, 1, 'C');
$pdf->Ln(2);

$qrData = $ticketNumero;
$qrUrl = 'https://chart.googleapis.com/chart?cht=qr&chs=150x150&chl=' . urlencode($qrData);

// Intentar obtener la imagen (file_get_contents o cURL)
$qrImage = false;
if (ini_get('allow_url_fopen')) {
    $qrImage = @file_get_contents($qrUrl);
} elseif (function_exists('curl_init')) {
    $ch = curl_init($qrUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $qrImage = curl_exec($ch);
    curl_close($ch);
}

if ($qrImage !== false && $qrImage !== null) {
    // Guardar en archivo temporal (con extensión .png)
    $qrTmp = tempnam(sys_get_temp_dir(), 'qr_') . '.png';
    if (@file_put_contents($qrTmp, $qrImage) !== false) {
        // Insertar imagen centrada
        $x = ($pdf->GetPageWidth() - 50) / 2; // centrar horizontalmente (A4 ancho)
        $y = $pdf->GetY();
        $pdf->Image($qrTmp, $x, $y, 50, 50);
        $pdf->Ln(52);
        @unlink($qrTmp);
    } else {
        $pdf->Cell(0, 6, utf8_decode('QR no disponible (no se pudo guardar temporalmente)'), 0, 1, 'C');
    }
} else {
    // No se pudo generar QR, mostramos texto alternativo
    $pdf->Cell(0, 6, utf8_decode('QR no disponible'), 0, 1, 'C');
}

$pdf->Ln(2);
// Nota al pie
$pdf->SetFont('Arial', 'I', 7);
$pdf->Cell(0, 4, utf8_decode('Este comprobante es válido para realizar el pago de los cursos mencionados.'), 0, 1, 'C');
$pdf->Cell(0, 4, utf8_decode('Conserve este documento para cualquier reclamo posterior.'), 0, 1, 'C');

// ============================================
// SALIDA DEL PDF
// ============================================
$safeDni = $alumno['dni'] ?: $alumnoId;
$safeDni = preg_replace('/[^0-9A-Za-z\-]/', '', $safeDni);
$nombreArchivo = 'Ticket_Pago_' . $safeDni . '_' . date('Ymd') . '.pdf';
// Mostrar en navegador
$pdf->Output($nombreArchivo, 'I');
exit;
