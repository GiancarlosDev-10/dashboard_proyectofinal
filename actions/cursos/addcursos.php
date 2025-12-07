<?php
include("../../db.php");

$errores = [];

// Recibir datos del formulario
$nombre        = trim($_POST['nombre'] ?? '');
$categoria_id  = trim($_POST['categoria_id'] ?? '');
$modalidad_id  = trim($_POST['modalidad_id'] ?? '');
$docente_id    = trim($_POST['docente_id'] ?? '');
$fecha_inicio  = trim($_POST['fecha_inicio'] ?? '');
$duracion      = trim($_POST['duracion'] ?? '');
$cupos         = trim($_POST['cupos'] ?? '');
$precio        = trim($_POST['precio'] ?? '');
$estado        = trim($_POST['estado'] ?? 'Activo');

// VALIDACIONES
if ($nombre === '')             $errores[] = "El nombre es obligatorio.";
if ($categoria_id === '' || !is_numeric($categoria_id))
    $errores[] = "La categoría es obligatoria.";
if ($modalidad_id === '' || !is_numeric($modalidad_id))
    $errores[] = "La modalidad es obligatoria.";
if ($docente_id === '' || !is_numeric($docente_id))
    $errores[] = "El docente es obligatorio.";
if ($fecha_inicio === '')       $errores[] = "La fecha de inicio es obligatoria.";
if ($duracion === '' || !is_numeric($duracion))
    $errores[] = "La duración debe ser un número.";
if ($cupos === '' || !is_numeric($cupos))
    $errores[] = "Los cupos deben ser un número.";
if ($precio === '' || !is_numeric($precio))
    $errores[] = "El precio debe ser un número.";

// SI NO HAY ERRORES → INSERTAR
if (empty($errores)) {

    $sql = "INSERT INTO curso 
            (nombre, categoria_id, modalidad_id, docente_id, fecha_inicio, duracion, cupos, precio, estado)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "siiisiiis",
        $nombre,
        $categoria_id,
        $modalidad_id,
        $docente_id,
        $fecha_inicio,
        $duracion,
        $cupos,
        $precio,
        $estado
    );

    $stmt->execute();

    // Redirigir exitosamente
    header("Location: indexcursos.php?add=ok");
    exit;
} else {

    // Redirigir mostrando los errores
    $params = http_build_query([
        'error' => implode('|', $errores)
    ]);

    header("Location: indexcursos.php?$params#addModal");
    exit;
}
