<?php

/**
 * ELIMINAR MATRÍCULA
 * Validaciones completas y respuesta JSON para AJAX
 */

include("../../db.php");

// Respuesta JSON
header('Content-Type: application/json');

$response = [
    'success'   => false,
    'message'   => '',
    'matricula' => null
];

// ==============================
// VERIFICAR MÉTODO
// ==============================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = "Método no permitido. Use POST.";
    echo json_encode($response);
    exit;
}

// ==============================
// VALIDAR ID
// ==============================

$id = intval(trim($_POST['id'] ?? ''));

if ($id <= 0) {
    $response['message'] = "ID inválido.";
    echo json_encode($response);
    exit;
}

try {

    // ==============================
    // 1️⃣ VERIFICAR EXISTENCIA
    // ==============================

    $stmt_check = $conn->prepare(
        "SELECT m.id,
                a.nombre AS alumno,
                c.nombre AS curso
         FROM matricula m
         JOIN alumno a ON m.alumno_id = a.id
         JOIN curso c  ON m.curso_id = c.id
         WHERE m.id = ?"
    );
    $stmt_check->bind_param("i", $id);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows === 0) {
        $response['message'] = "No se encontró la matrícula con ID $id.";
        $stmt_check->close();
        $conn->close();
        echo json_encode($response);
        exit;
    }

    $matricula = $result->fetch_assoc();
    $stmt_check->close();

    // ==============================
    // 2️⃣ ELIMINAR MATRÍCULA
    // ==============================

    $stmt_delete = $conn->prepare("DELETE FROM matricula WHERE id = ?");
    $stmt_delete->bind_param("i", $id);
    $stmt_delete->execute();

    if ($stmt_delete->affected_rows > 0) {

        error_log(
            "MATRÍCULA ELIMINADA - ID: {$matricula['id']} - Alumno: {$matricula['alumno']} - Curso: {$matricula['curso']}"
        );

        $response['success']   = true;
        $response['message']   = "Matrícula eliminada exitosamente.";
        $response['matricula'] = $matricula;
    } else {
        $response['message'] = "No se pudo eliminar la matrícula. Puede que ya haya sido eliminada.";
    }

    $stmt_delete->close();
} catch (Exception $e) {

    error_log("ERROR DELETE MATRÍCULA ID $id: " . $e->getMessage());

    if (
        strpos($e->getMessage(), 'foreign key constraint') !== false ||
        strpos($e->getMessage(), 'FOREIGN KEY') !== false
    ) {
        $response['message'] = "No se puede eliminar. La matrícula tiene registros relacionados.";
    } else {
        $response['message'] = "Error del sistema. No se pudo eliminar la matrícula.";
    }
}

$conn->close();

// Enviar JSON
echo json_encode($response);
exit;
