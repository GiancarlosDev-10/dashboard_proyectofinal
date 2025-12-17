<?php
session_start();
date_default_timezone_set('America/Lima'); // ← hora de Perú
require(__DIR__ . '/../vendor/fpdf/fpdf.php');
require(__DIR__ . '/../db.php');

// ⚠ Recuperar datos del usuario logueado
$usuario = $_SESSION['admin_name'] ?? 'Usuario';
$correo  = $_SESSION['admin_email'] ?? 'Correo no disponible';
// Sanitizar valores para evitar contenido inesperado en el PDF
$usuario = strip_tags($usuario);
$usuario = preg_replace('/[\x00-\x1F\x7F]/', '', $usuario);
$correo = strip_tags($correo);
$correo = preg_replace('/[\x00-\x1F\x7F]/', '', $correo);
$fecha   = date('d/m/Y H:i:s');

// Crear PDF
$pdf = new FPDF();
$pdf->AddPage();

// ===============================
// LOGO CENTRADO
// ===============================
$pdf->Image(__DIR__ . '/../img/logo_cersa.png', 80, 10, 50);
$pdf->Ln(30); // espacio debajo del logo

// ===============================
// TÍTULO PRINCIPAL
// ===============================
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, utf8_decode('REGISTRO DE ALUMNOS'), 0, 1, 'C');
$pdf->Ln(5);

// ===============================
// DATOS DEL USUARIO
// ===============================
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, utf8_decode("Generado por: $usuario"), 0, 1);
$pdf->Cell(0, 8, utf8_decode("Correo: $correo"), 0, 1);
$pdf->Cell(0, 8, utf8_decode("Fecha y hora: $fecha"), 0, 1);
$pdf->Ln(5);

// ===============================
// TABLA
// ===============================

// Encabezado gris
$pdf->SetFillColor(230, 230, 230);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(80, 8, 'Nombre', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'DNI', 1, 0, 'C', true);
$pdf->Cell(70, 8, 'Email', 1, 1, 'C', true);

// Datos
$pdf->SetFont('Arial', '', 10);

$consulta = $conn->query("SELECT nombre, dni, email FROM alumno ORDER BY nombre ASC");

while ($row = $consulta->fetch_assoc()) {
    $nombre = strip_tags($row['nombre']);
    $nombre = preg_replace('/[\x00-\x1F\x7F]/', '', $nombre);
    $dni = preg_replace('/[^0-9A-Za-z\-]/', '', $row['dni']);
    $email = strip_tags($row['email']);
    $email = preg_replace('/[\x00-\x1F\x7F]/', '', $email);

    $pdf->Cell(80, 8, utf8_decode($nombre), 1);
    $pdf->Cell(40, 8, $dni, 1);
    $pdf->Cell(70, 8, utf8_decode($email), 1);
    $pdf->Ln();
}

$pdf->Output('I', 'Reporte_Alumnos.pdf');
exit;
