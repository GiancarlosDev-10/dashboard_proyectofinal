<?php
include("../../db.php");

// Validar ID desde GET
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: indexmatriculas.php?error=ID inválido");
    exit;
}

$id = intval(trim($_GET['id']));

// Verificar que la matrícula exista
$check = $conn->prepare("SELECT id FROM matricula WHERE id = ?");
$check->bind_param("i", $id);
$check->execute();
$exists = $check->get_result()->num_rows;

if ($exists == 0) {
    header("Location: indexmatriculas.php?error=No se encontró la matrícula");
    exit;
}

// Eliminar matrícula
$sql = "DELETE FROM matricula WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

// Redirección exitosa
header("Location: indexmatriculas.php?delete=ok");
exit;
