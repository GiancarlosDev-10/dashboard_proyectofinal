<?php
include("../../db.php");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: indexcursos.php?error=ID inválido");
    exit;
}

$id = intval(trim($_GET['id']));

// Verificar si el curso existe
$check = $conn->prepare("SELECT id FROM curso WHERE id = ?");
$check->bind_param("i", $id);
$check->execute();
$exists = $check->get_result()->num_rows;

if ($exists == 0) {
    header("Location: indexcursos.php?error=No se encontró el curso");
    exit;
}

// Eliminar
$sql = "DELETE FROM curso WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: indexcursos.php?delete=ok");
exit;
