<?php

/**
 * EDITAR DOCENTE
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
    $response['message'] = "ID de docente no proporcionado.";
    echo json_encode($response);
    exit;
}

$id = intval($_POST['id']);

// ==============================
// SANITIZAR Y OBTENER DATOS
// ==============================

$nombre       = trim($_POST['nombre'] ?? '');
$especialidad = trim($_POST['especialidad'] ?? '');
$dni          = trim($_POST['dni'] ?? '');

// ==============================
// VALIDACIONES SERVER-SIDE
// ==============================

// Nombre
if ($nombre === '') {
    $response['errors']['nombre'] = "El nombre es obligatorio.";
} elseif (strlen($nombre) < 3) {
    $response['errors']['nombre'] = "El nombre debe tener al menos 3 caracteres.";
}

// Especialidad
if ($especialidad === '') {
    $response['errors']['especialidad'] = "La especialidad es obligatoria.";
} elseif (strlen($especialidad) < 3) {
    $response['errors']['especialidad'] = "La especialidad debe tener al menos 3 caracteres.";
}

// DNI
if ($dni === '') {
    $response['errors']['dni'] = "El DNI es obligatorio.";
} elseif (!preg_match('/^[0-9]{8}$/', $dni)) {
    $response['errors']['dni'] = "El DNI debe tener exactamente 8 dígitos numéricos.";
} else {
    // Verificar duplicado en otro docente
    try {
        $stmt_check = $conn->prepare(
            "SELECT id FROM docente WHERE dni = ? AND id != ?"
        );
        $stmt_check->bind_param("si", $dni, $id);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows > 0) {
            $response['errors']['dni'] = "El DNI ya está registrado en otro docente.";
        }
        $stmt_check->close();
    } catch (Exception $e) {
        error_log("ERROR CHECK DNI DOCENTE (EDIT): " . $e->getMessage());
        $response['errors']['dni'] = "Error al verificar el DNI.";
    }
}

// ==============================
// ACTUALIZAR SI NO HAY ERRORES
// ==============================

if (empty($response['errors'])) {

    try {

        $sql = "UPDATE docente
                SET nombre = ?, especialidad = ?, dni = ?
                WHERE id = ?";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("sssi", $nombre, $especialidad, $dni, $id);

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        // affected_rows puede ser 0 si no hubo cambios
        $response['success'] = true;
        $response['message'] = "Docente actualizado exitosamente.";

        error_log("DOCENTE EDITADO - ID: $id - Nombre: $nombre - DNI: $dni");

        $stmt->close();
    } catch (Exception $e) {

        error_log("ERROR EDIT DOCENTE ID $id: " . $e->getMessage());
        $response['message'] = "Error del sistema. No se pudo actualizar el docente.";
    }
} else {
    $response['message'] = "Por favor corrige los errores del formulario.";
}

$conn->close();

// Enviar JSON
echo json_encode($response);
exit;
