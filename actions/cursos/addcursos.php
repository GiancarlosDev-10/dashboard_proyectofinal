<?php
include("../../db.php");


$errores = [];

// Recibir datos del formulario
$nombre        = trim($_POST['nombre'] ?? '');
$categoria_id  = trim($_POST['categoria_id'] ?? '');
$modalidad_id  = trim($_POST['modalidad_id'] ?? '');
$fecha_inicio  = trim($_POST['fecha_inicio'] ?? '');
$cupos         = trim($_POST['cupos'] ?? '');
$precio        = trim($_POST['precio'] ?? '');
$estado        = trim($_POST['estado'] ?? 'Activo');

// VALIDACIONES
if ($nombre === '')
    $errores[] = "El nombre del curso es obligatorio.";

if ($categoria_id === '' || !is_numeric($categoria_id))
    $errores[] = "Debe seleccionar una categoría.";

if ($modalidad_id === '' || !is_numeric($modalidad_id))
    $errores[] = "Debe seleccionar una modalidad.";

if ($fecha_inicio === '')
    $errores[] = "La fecha de inicio es obligatoria.";

if ($cupos === '' || !is_numeric($cupos))
    $errores[] = "Los cupos deben ser un número.";

if ($precio === '' || !is_numeric($precio))
    $errores[] = "El precio debe ser numérico.";

// SI NO HAY ERRORES → INSERTAR
if (empty($errores)) {

    $sql = "INSERT INTO curso 
            (nombre, categoria_id, modalidad_id, fecha_inicio, cupos, precio, estado)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "siisids",
        $nombre,
        $categoria_id,
        $modalidad_id,
        $fecha_inicio,
        $cupos,
        $precio,
        $estado
    );

    $stmt->execute();
    if ($stmt->error) {
        die("ERROR SQL: " . $stmt->error);
    }

    header("Location: indexcursos.php?add=ok");
    exit;
} else {

    // Redirigir mostrando errores y reabrir modal
    $params = http_build_query([
        'error' => implode('|', $errores)
    ]);

    header("Location: indexcursos.php?$params#addModal");
    exit;
}
