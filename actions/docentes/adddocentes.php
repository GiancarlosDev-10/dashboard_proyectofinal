<?php

/**
 * AGREGAR DOCENTE
 * Validaciones server-side + respuesta JSON para AJAX
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
    // Verificar duplicado
    try {
        $stmt_check = $conn->prepare("SELECT id FROM docente WHERE dni = ?");
        $stmt_check->bind_param("s", $dni);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows > 0) {
            $response['errors']['dni'] = "El DNI ya está registrado en otro docente.";
        }
        $stmt_check->close();
    } catch (Exception $e) {
        error_log("ERROR CHECK DNI DOCENTE: " . $e->getMessage());
        $response['errors']['dni'] = "Error al verificar el DNI.";
    }
}

// ==============================
// INSERTAR SI NO HAY ERRORES
// ==============================

if (empty($response['errors'])) {

    try {

        $sql = "INSERT INTO docente (nombre, especialidad, dni)
                VALUES (?, ?, ?)";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("sss", $nombre, $especialidad, $dni);

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        if ($stmt->affected_rows > 0) {

            $response['success'] = true;
            $response['message'] = "Docente registrado exitosamente.";
            $response['docente_id'] = $stmt->insert_id;

            error_log("DOCENTE AGREGADO - ID: {$stmt->insert_id} - Nombre: $nombre - DNI: $dni");
        } else {
            throw new Exception("No se pudo insertar el docente.");
        }

        $stmt->close();
    } catch (Exception $e) {

        error_log("ERROR ADD DOCENTE: " . $e->getMessage());
        $response['message'] = "Error del sistema. No se pudo registrar el docente.";
    }
} else {
    $response['message'] = "Por favor corrige los errores del formulario.";
}

$conn->close();

// Enviar JSON
echo json_encode($response);
exit;
