<?php
include("../../db.php");

$alumno_id         = trim($_POST['alumno_id'] ?? '');
$curso_id          = trim($_POST['curso_id'] ?? '');
$fecha_inscripcion = trim($_POST['fecha_inscripcion'] ?? '');
$estado            = trim($_POST['estado'] ?? 'Matriculado');

// VALIDACIONES
$errores = [];

if ($alumno_id === '' || !ctype_digit($alumno_id))
    $errores[] = "Debe seleccionar un alumno.";

if ($curso_id === '' || !ctype_digit($curso_id))
    $errores[] = "Debe seleccionar un curso.";

if ($fecha_inscripcion === '')
    $errores[] = "La fecha de inscripción es obligatoria.";

if ($estado === '')
    $errores[] = "Debe seleccionar un estado válido.";

// SI NO HAY ERRORES
if (empty($errores)) {

    $sql = "INSERT INTO matricula 
            (alumno_id, curso_id, fecha_inscripcion, estado)
            VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss", $alumno_id, $curso_id, $fecha_inscripcion, $estado);
    $stmt->execute();

    header("Location: indexmatriculas.php?add=ok");
    exit;
} else {

    $params = http_build_query(['error' => implode('|', $errores)]);
    header("Location: indexmatriculas.php?$params#addModal");
    exit;
}
