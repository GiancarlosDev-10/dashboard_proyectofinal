<?php
session_start();
date_default_timezone_set('America/Lima');

require(__DIR__ . '/../vendor/fpdf/fpdf.php');

// ===============================
// DATOS GENERALES
// ===============================
$usuario = $_SESSION['admin_name'] ?? 'Administrador';
// Sanitizar nombre de usuario para evitar inserción de etiquetas o caracteres de control
$usuario = strip_tags($usuario);
$usuario = preg_replace('/[\x00-\x1F\x7F]/', '', $usuario);
$fecha   = date('d/m/Y H:i:s');

// ===============================
// CREAR PDF
// ===============================
$pdf = new FPDF();
$pdf->AddPage();

// ===============================
// LOGO
// ===============================
$pdf->Image(__DIR__ . '/../img/logo_cersa.png', 80, 10, 50);
$pdf->Ln(30);

// ===============================
// TÍTULO
// ===============================
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, utf8_decode('DOCUMENTACIÓN DEL SISTEMA'), 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, utf8_decode('Sistema de Gestión Académica CERSA'), 0, 1, 'C');
$pdf->Ln(8);

// ===============================
// DATOS DEL REPORTE
// ===============================
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 8, utf8_decode("Generado por: $usuario"), 0, 1);
$pdf->Cell(0, 8, utf8_decode("Fecha y hora: $fecha"), 0, 1);
$pdf->Ln(6);

// ===============================
// INTRODUCCIÓN
// ===============================
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, utf8_decode('1. Introducción'), 0, 1);
$pdf->SetFont('Arial', '', 11);
$pdf->MultiCell(0, 7, utf8_decode(
    "El Sistema de Gestión Académica CERSA es una aplicación web desarrollada en PHP y MySQL, "
        . "diseñada para administrar de manera integral los procesos académicos de una institución educativa. "
        . "El sistema permite gestionar alumnos, docentes, cursos y matrículas, así como generar reportes "
        . "dinámicos y exportarlos en formato PDF."
));
$pdf->Ln(4);

// ===============================
// FUNCIONALIDADES
// ===============================
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, utf8_decode('2. Funcionalidades Principales'), 0, 1);
$pdf->SetFont('Arial', '', 11);

$pdf->MultiCell(0, 7, utf8_decode(
    "- Autenticación de administrador mediante login seguro.\n"
        . "- Gestión completa de alumnos (registro, edición y eliminación).\n"
        . "- Gestión de docentes y asignación académica.\n"
        . "- Administración de cursos con categorías y modalidades.\n"
        . "- Registro y control de matrículas.\n"
        . "- Generación de reportes dinámicos y exportación en PDF."
));
$pdf->Ln(4);

// ===============================
// DASHBOARD
// ===============================
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, utf8_decode('3. Dashboard Administrativo'), 0, 1);
$pdf->SetFont('Arial', '', 11);

$pdf->MultiCell(0, 7, utf8_decode(
    "El dashboard administrativo presenta indicadores en tiempo real que permiten visualizar "
        . "el estado general del sistema, incluyendo totales de alumnos, cursos y docentes, así como "
        . "ganancias acumuladas. Además, se incluyen gráficos dinámicos que facilitan el análisis "
        . "de la información académica."
));
$pdf->Ln(4);

// ===============================
// REPORTES PDF
// ===============================
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, utf8_decode('4. Reportes en PDF'), 0, 1);
$pdf->SetFont('Arial', '', 11);

$pdf->MultiCell(0, 7, utf8_decode(
    "El sistema permite generar reportes institucionales en formato PDF, los cuales incluyen:\n"
        . "- Logo institucional.\n"
        . "- Usuario que genera el reporte.\n"
        . "- Fecha y hora de generación.\n"
        . "- Tablas ordenadas y sin identificadores internos.\n\n"
        . "Reportes disponibles:\n"
        . "- Reporte General de Alumnos.\n"
        . "- Reporte General de Docentes.\n"
        . "- Reporte General de Cursos.\n"
        . "- Reporte General de Matrículas."
));
$pdf->Ln(4);

// ===============================
// TECNOLOGÍAS
// ===============================
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, utf8_decode('5. Tecnologías Utilizadas'), 0, 1);
$pdf->SetFont('Arial', '', 11);

$pdf->MultiCell(0, 7, utf8_decode(
    "- PHP 8\n"
        . "- MySQL\n"
        . "- FPDF (generación de PDFs)\n"
        . "- Chart.js (gráficos dinámicos)\n"
        . "- Bootstrap 4\n"
        . "- SB Admin 2\n"
        . "- HTML5, CSS3 y JavaScript"
));
$pdf->Ln(4);

// ===============================
// ESTRUCTURA
// ===============================
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, utf8_decode('6. Estructura General del Proyecto'), 0, 1);
$pdf->SetFont('Arial', '', 11);

$pdf->MultiCell(0, 7, utf8_decode(
    "El proyecto se organiza en módulos independientes para facilitar el mantenimiento "
        . "y la escalabilidad del sistema. La estructura separa la lógica del negocio, "
        . "componentes reutilizables, recursos gráficos y reportes."
));
$pdf->Ln(4);

// ===============================
// ESTADO DEL PROYECTO
// ===============================
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, utf8_decode('7. Estado del Proyecto'), 0, 1);
$pdf->SetFont('Arial', '', 11);

$pdf->MultiCell(0, 7, utf8_decode(
    "El sistema se encuentra completamente funcional y listo para su presentación académica. "
        . "Como mejoras futuras se contempla la implementación de roles de usuario, "
        . "encriptación de contraseñas y ampliación de reportes."
));
$pdf->Ln(6);

// ===============================
// AUTOR
// ===============================
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, utf8_decode('Autor'), 0, 1);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 7, utf8_decode('Giancarlos'), 0, 1);
$pdf->Cell(0, 7, utf8_decode('Proyecto académico - Sistema de Gestión Académica'), 0, 1);
$pdf->Cell(0, 7, utf8_decode('Perú'), 0, 1);

// ===============================
// SALIDA
// ===============================
$pdf->Output('I', 'Documentacion_CERSA.pdf');
exit;
