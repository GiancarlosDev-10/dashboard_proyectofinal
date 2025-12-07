<?php
include("../../db.php");

$errores = [];

// Recibir datos
$alumno_id        = trim($_POST['alumno_id'] ?? '');
$curso_id         = trim($_POST['curso_id'] ?? '');
$estado           = trim($_POST['estado'] ?? '');
$fecha_inscripcion = trim($_POST['fecha_inscripcion'] ?? '');

// VALIDACIONES
if ($alumno_id === '' || !is_numeric($alumno_id))
    $errores[] = "Debe seleccionar un alumno.";

if ($curso_id === '' || !is_numeric($curso_id))
    $errores[] = "Debe seleccionar un curso.";

if ($estado === '')
    $errores[] = "Debe seleccionar un estado.";

if ($fecha_inscripcion === '')
    $errores[] = "Debe ingresar la fecha de inscripción.";


// SI NO HAY ERRORES → INSERTAR
if (empty($errores)) {

    $sql = "INSERT INTO matricula (alumno_id, curso_id, estado, fecha_inscripcion) 
            VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "iiss",
        $alumno_id,
        $curso_id,
        $estado,
        $fecha_inscripcion
    );

    $stmt->execute();

    header("Location: indexmatriculas.php?add=ok");
    exit;
} else {

    // Enviar errores al modal
    $params = http_build_query([
        'error' => implode('|', $errores)
    ]);

    header("Location: indexmatriculas.php?$params#addModal");
    exit;
}
