<?php

/**
 * AGREGAR MATRÍCULA
 * Validaciones server-side completas y respuesta JSON para AJAX
 */

session_start();
require '../../includes/csrf.php';
include("../../db.php");

// VALIDAR CSRF PRIMERO
verificar_csrf_o_morir();

// Respuesta JSON
header('Content-Type: application/json');

$response = [
    'success' => false,
    'errors'  => [],
    'message' => ''
];

// ==============================
// SANITIZAR Y OBTENER DATOS
// ==============================

$alumno_id         = intval($_POST['alumno_id'] ?? 0);
$curso_id          = intval($_POST['curso_id'] ?? 0);
$fecha_inscripcion = trim($_POST['fecha_inscripcion'] ?? '');
$estado            = trim($_POST['estado'] ?? 'Matriculado');

// ==============================
// VALIDACIONES SERVER-SIDE
// ==============================

// Alumno
if ($alumno_id <= 0) {
    $response['errors']['alumno_id'] = "Debe seleccionar un alumno válido.";
}

// Curso
if ($curso_id <= 0) {
    $response['errors']['curso_id'] = "Debe seleccionar un curso válido.";
}

// Fecha
if ($fecha_inscripcion === '') {
    $response['errors']['fecha_inscripcion'] = "La fecha de inscripción es obligatoria.";
} elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_inscripcion) || strtotime($fecha_inscripcion) === false) {
    $response['errors']['fecha_inscripcion'] = "Formato de fecha inválido.";
}

// Estado
if (!in_array($estado, ['Matriculado', 'Pendiente'])) {
    $response['errors']['estado'] = "Estado inválido.";
}

// ==============================
// VALIDAR DUPLICADO (MISMO ALUMNO + MISMO CURSO)
// ==============================

if (empty($response['errors'])) {
    try {
        $stmt_check = $conn->prepare(
            "SELECT id FROM matricula WHERE alumno_id = ? AND curso_id = ?"
        );
        $stmt_check->bind_param("ii", $alumno_id, $curso_id);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows > 0) {
            $response['errors']['curso_id'] = "Este alumno ya está matriculado en este curso.";
        }

        $stmt_check->close();
    } catch (Exception $e) {
        error_log("ERROR CHECK DUPLICADO MATRÍCULA: " . $e->getMessage());
        $response['message'] = "Error al verificar la matrícula.";
    }
}

// ==============================
// INSERTAR SI NO HAY ERRORES
// ==============================

if (empty($response['errors'])) {

    try {

        $sql = "INSERT INTO matricula
                (alumno_id, curso_id, fecha_inscripcion, estado)
                VALUES (?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param(
            "iiss",
            $alumno_id,
            $curso_id,
            $fecha_inscripcion,
            $estado
        );

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        if ($stmt->affected_rows > 0) {

            $response['success'] = true;
            $response['message'] = "Matrícula registrada exitosamente.";
            $response['matricula_id'] = $stmt->insert_id;

            error_log(
                "MATRÍCULA AGREGADA - ID: {$stmt->insert_id} - Alumno: $alumno_id - Curso: $curso_id"
            );
        } else {
            throw new Exception("No se pudo insertar la matrícula.");
        }

        $stmt->close();
    } catch (Exception $e) {

        error_log("ERROR ADD MATRÍCULA: " . $e->getMessage());
        $response['message'] = "Error del sistema. No se pudo registrar la matrícula.";
    }
} else {
    $response['message'] = "Por favor corrige los errores del formulario.";
}

$conn->close();

// Enviar JSON
echo json_encode($response);
exit;
