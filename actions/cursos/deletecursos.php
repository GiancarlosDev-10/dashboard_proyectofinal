<?php
session_start();
require '../../includes/csrf.php';
include("../../db.php");

// VALIDAR CSRF PRIMERO
verificar_csrf_o_morir();

// Configurar respuesta JSON
header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => '',
    'curso'   => null
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
        "SELECT id, nombre, fecha_inicio, estado 
         FROM curso 
         WHERE id = ?"
    );
    $stmt_check->bind_param("i", $id);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows === 0) {
        $response['message'] = "No se encontró el curso con ID $id.";
        $stmt_check->close();
        $conn->close();
        echo json_encode($response);
        exit;
    }

    $curso = $result->fetch_assoc();
    $stmt_check->close();

    // ==============================
    // 2️⃣ VERIFICAR RELACIONES
    // ==============================
    // Ejemplo: tabla matriculas

    /*
    $stmt_matriculas = $conn->prepare(
        "SELECT COUNT(*) AS total 
         FROM matricula 
         WHERE curso_id = ?"
    );
    $stmt_matriculas->bind_param("i", $id);
    $stmt_matriculas->execute();
    $matriculas = $stmt_matriculas->get_result()->fetch_assoc();
    $stmt_matriculas->close();

    if ($matriculas['total'] > 0) {
        $response['message'] = "No se puede eliminar. El curso tiene {$matriculas['total']} matrícula(s) registradas.";
        $conn->close();
        echo json_encode($response);
        exit;
    }
    */

    // ==============================
    // 3️⃣ ELIMINAR CURSO
    // ==============================

    $stmt_delete = $conn->prepare("DELETE FROM curso WHERE id = ?");
    $stmt_delete->bind_param("i", $id);
    $stmt_delete->execute();

    if ($stmt_delete->affected_rows > 0) {

        error_log(
            "CURSO ELIMINADO - ID: {$curso['id']} - Nombre: {$curso['nombre']} - Inicio: {$curso['fecha_inicio']}"
        );

        $response['success'] = true;
        $response['message'] = "Curso '{$curso['nombre']}' eliminado exitosamente.";
        $response['curso']   = $curso;
    } else {
        $response['message'] = "No se pudo eliminar el curso. Puede que ya haya sido eliminado.";
    }

    $stmt_delete->close();
} catch (Exception $e) {

    error_log("ERROR AL ELIMINAR CURSO ID $id: " . $e->getMessage());

    if (
        strpos($e->getMessage(), 'foreign key constraint') !== false ||
        strpos($e->getMessage(), 'FOREIGN KEY') !== false
    ) {
        $response['message'] = "No se puede eliminar. El curso tiene registros relacionados (matrículas, evaluaciones, etc.).";
    } else {
        $response['message'] = "Error del sistema. No se pudo eliminar el curso.";
    }
}

$conn->close();

echo json_encode($response);
exit;
