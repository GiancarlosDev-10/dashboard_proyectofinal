<?php
include("../../db.php");

$id = intval(trim($_GET['id'] ?? ''));
// Validar ID
if ($id <= 0) {
    header("Location: indexalumno.php?delete_error=ID inválido");
    exit;
}
// Verificar existencia
$check = $conn->prepare("SELECT id FROM alumno WHERE id = ?");
$check->bind_param("i", $id);
$check->execute();
$exists = $check->get_result()->num_rows;

if ($exists == 0) {
    header("Location: indexalumno.php?delete_error=No se encontró el alumno");
    exit;
}
// Eliminar
$sql = "DELETE FROM alumno WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
header("Location: indexalumno.php?delete=ok");
exit;
