<?php
include("../../db.php");

$errores = [];

$id                = trim($_POST['id'] ?? '');
$curso_id          = trim($_POST['curso_id'] ?? '');
$fecha_inscripcion = trim($_POST['fecha_inscripcion'] ?? '');
$estado            = trim($_POST['estado'] ?? '');

if ($id === '' || !ctype_digit($id))
    $errores[] = "ID inválido.";

if ($curso_id === '' || !ctype_digit($curso_id))
    $errores[] = "Debe seleccionar un curso.";

if ($fecha_inscripcion === '')
    $errores[] = "Debe seleccionar la fecha de inscripción.";

if ($estado === '')
    $errores[] = "Debe seleccionar un estado.";

if (empty($errores)) {

    $sql = "UPDATE matricula
            SET curso_id = ?, fecha_inscripcion = ?, estado = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issi", $curso_id, $fecha_inscripcion, $estado, $id);
    $stmt->execute();

    header("Location: indexmatriculas.php?edit=ok");
    exit;
} else {

    $params = http_build_query(['error' => implode('|', $errores)]);
    header("Location: indexmatriculas.php?$params#editModal");
    exit;
}
