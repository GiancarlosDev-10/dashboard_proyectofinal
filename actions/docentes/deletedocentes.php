<?php
include("../../db.php");

// Validar ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: indexdocentes.php?error=ID inválido");
    exit;
}

$id = intval(trim($_GET['id']));

// Verificar si el docente existe
$check = $conn->prepare("SELECT id FROM docente WHERE id = ?");
$check->bind_param("i", $id);
$check->execute();
$exists = $check->get_result()->num_rows;

if ($exists == 0) {
    header("Location: indexdocentes.php?error=No se encontró el docente");
    exit;
}

// Eliminar docente
$sql = "DELETE FROM docente WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

// Si tu tabla curso tiene FK hacia docente,  
// recuerda aplicar ON DELETE CASCADE igual que hicimos con alumnos y cursos.

header("Location: indexdocentes.php?delete=ok");
exit;
