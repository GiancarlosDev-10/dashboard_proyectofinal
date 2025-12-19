<?php

/**
 * ELIMINAR DOCENTE
 * Con validaciones completas, verificación de relaciones y respuesta JSON
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
    'message' => '',
    'docente' => null
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
        "SELECT id, nombre, especialidad, dni
         FROM docente
         WHERE id = ?"
    );
    $stmt_check->bind_param("i", $id);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows === 0) {
        $response['message'] = "No se encontró el docente con ID $id.";
        $stmt_check->close();
        $conn->close();
        echo json_encode($response);
        exit;
    }

    $docente = $result->fetch_assoc();
    $stmt_check->close();

    // ==============================
    // 2️⃣ VERIFICAR RELACIONES
    // ==============================
    // Verificar si el docente tiene cursos asignados

    $stmt_rel = $conn->prepare(
        "SELECT COUNT(*) AS total
         FROM curso
         WHERE docente_id = ?"
    );
    $stmt_rel->bind_param("i", $id);
    $stmt_rel->execute();
    $rel = $stmt_rel->get_result()->fetch_assoc();
    $stmt_rel->close();

    if ($rel['total'] > 0) {
        $response['message'] = "No se puede eliminar. El docente tiene {$rel['total']} curso(s) asignado(s).";
        $conn->close();
        echo json_encode($response);
        exit;
    }

    // ==============================
    // 3️⃣ ELIMINAR DOCENTE
    // ==============================

    $stmt_delete = $conn->prepare("DELETE FROM docente WHERE id = ?");
    $stmt_delete->bind_param("i", $id);
    $stmt_delete->execute();

    if ($stmt_delete->affected_rows > 0) {

        error_log(
            "DOCENTE ELIMINADO - ID: {$docente['id']} - Nombre: {$docente['nombre']} - DNI: {$docente['dni']} - Especialidad: {$docente['especialidad']}"
        );

        $response['success'] = true;
        $response['message'] = "Docente '{$docente['nombre']}' eliminado exitosamente.";
        $response['docente'] = $docente;
    } else {
        $response['message'] = "No se pudo eliminar el docente. Puede que ya haya sido eliminado.";
    }

    $stmt_delete->close();
} catch (Exception $e) {

    error_log("ERROR AL ELIMINAR DOCENTE ID $id: " . $e->getMessage());

    if (
        strpos($e->getMessage(), 'foreign key constraint') !== false ||
        strpos($e->getMessage(), 'FOREIGN KEY') !== false
    ) {
        $response['message'] = "No se puede eliminar. El docente tiene registros relacionados (cursos asignados).";
    } else {
        $response['message'] = "Error del sistema. No se pudo eliminar el docente.";
    }
}

$conn->close();

// Enviar JSON
echo json_encode($response);
exit;
