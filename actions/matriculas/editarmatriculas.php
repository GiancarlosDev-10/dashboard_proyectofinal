<?php

/**
 * EDITAR MATRÍCULA
 * Validaciones server-side completas y respuesta JSON para AJAX
 */

include("../../db.php");

// Respuesta JSON
header('Content-Type: application/json');

$response = [
    'success' => false,
    'errors'  => [],
    'message' => ''
];

// ==============================
// VALIDAR ID
// ==============================

if (empty($_POST['id'])) {
    $response['message'] = "ID de matrícula no proporcionado.";
    echo json_encode($response);
    exit;
}

$id = trim($_POST['id']);

if (!ctype_digit($id)) {
    $response['message'] = "ID inválido.";
    echo json_encode($response);
    exit;
}

// ==============================
// SANITIZAR Y OBTENER DATOS
// ==============================

$curso_id          = trim($_POST['curso_id'] ?? '');
$fecha_inscripcion = trim($_POST['fecha_inscripcion'] ?? '');
$estado            = trim($_POST['estado'] ?? '');

// ==============================
// VALIDACIONES SERVER-SIDE
// ==============================

// Curso
if ($curso_id === '' || !ctype_digit($curso_id)) {
    $response['errors']['curso_id'] = "Debe seleccionar un curso válido.";
}

// Fecha
if ($fecha_inscripcion === '') {
    $response['errors']['fecha_inscripcion'] = "Debe seleccionar la fecha de inscripción.";
} elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_inscripcion) || strtotime($fecha_inscripcion) === false) {
    $response['errors']['fecha_inscripcion'] = "Formato de fecha inválido.";
}

// Estado
if (!in_array($estado, ['Matriculado', 'Pendiente'])) {
    $response['errors']['estado'] = "Estado inválido.";
}

// ==============================
// ACTUALIZAR SI NO HAY ERRORES
// ==============================

if (empty($response['errors'])) {

    try {

        $sql = "UPDATE matricula
                SET curso_id = ?, fecha_inscripcion = ?, estado = ?
                WHERE id = ?";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param(
            "issi",
            $curso_id,
            $fecha_inscripcion,
            $estado,
            $id
        );

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        // affected_rows puede ser 0 si no hubo cambios
        $response['success'] = true;
        $response['message'] = "Matrícula actualizada exitosamente.";

        error_log(
            "MATRÍCULA EDITADA - ID: $id - Curso: $curso_id - Estado: $estado"
        );

        $stmt->close();
    } catch (Exception $e) {

        error_log("ERROR EDIT MATRÍCULA ID $id: " . $e->getMessage());
        $response['message'] = "Error del sistema. No se pudo actualizar la matrícula.";
    }
} else {
    $response['message'] = "Por favor corrige los errores del formulario.";
}

$conn->close();

// Enviar JSON
echo json_encode($response);
exit;
