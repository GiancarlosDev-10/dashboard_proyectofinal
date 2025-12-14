<?php

/**
 * EDITAR ALUMNO
 * Con validaciones server-side completas y respuesta JSON para AJAX
 */

include("../../db.php");

// Configurar respuesta JSON
header('Content-Type: application/json');

// Inicializar array de respuesta
$response = [
    'success' => false,
    'errors' => [],
    'message' => ''
];

// Verificar que se envió el ID
if (empty($_POST['id'])) {
    $response['message'] = "ID de alumno no proporcionado.";
    echo json_encode($response);
    exit;
}

// Sanitizar y obtener datos del POST
$id      = (int)$_POST['id'];
$nombre  = trim($_POST['nombre'] ?? '');
$dni     = trim($_POST['dni'] ?? '');
$email   = trim($_POST['email'] ?? '');
$celular = trim($_POST['celular'] ?? '');

// ============================================
// 1️⃣ VALIDACIONES SERVER-SIDE
// ============================================

// Validar nombre
if ($nombre === '') {
    $response['errors']['nombre'] = "El nombre es obligatorio.";
} elseif (strlen($nombre) < 3) {
    $response['errors']['nombre'] = "El nombre debe tener al menos 3 caracteres.";
} elseif (strlen($nombre) > 100) {
    $response['errors']['nombre'] = "El nombre no puede exceder 100 caracteres.";
}

// Validar DNI
if ($dni === '') {
    $response['errors']['dni'] = "El DNI es obligatorio.";
} elseif (!preg_match('/^[0-9]{8}$/', $dni)) {
    $response['errors']['dni'] = "El DNI debe tener exactamente 8 dígitos numéricos.";
} else {
    // Verificar si el DNI ya existe en OTRO alumno (excluyendo el actual)
    try {
        $stmt_check = $conn->prepare("SELECT id FROM alumno WHERE dni = ? AND id != ?");
        $stmt_check->bind_param("si", $dni, $id);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows > 0) {
            $response['errors']['dni'] = "El DNI ya está registrado en otro alumno.";
        }
        $stmt_check->close();
    } catch (Exception $e) {
        error_log("Error al verificar DNI: " . $e->getMessage());
        $response['errors']['dni'] = "Error al verificar el DNI. Intenta nuevamente.";
    }
}

// Validar email
if ($email === '') {
    $response['errors']['email'] = "El email es obligatorio.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['errors']['email'] = "El formato del email no es válido.";
} else {
    // Verificar si el email ya existe en OTRO alumno (excluyendo el actual)
    try {
        $stmt_check = $conn->prepare("SELECT id FROM alumno WHERE email = ? AND id != ?");
        $stmt_check->bind_param("si", $email, $id);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows > 0) {
            $response['errors']['email'] = "El email ya está registrado en otro alumno.";
        }
        $stmt_check->close();
    } catch (Exception $e) {
        error_log("Error al verificar email: " . $e->getMessage());
        $response['errors']['email'] = "Error al verificar el email. Intenta nuevamente.";
    }
}

// Validar celular
if ($celular === '') {
    $response['errors']['celular'] = "El celular es obligatorio.";
} elseif (!preg_match('/^[0-9]{9}$/', $celular)) {
    $response['errors']['celular'] = "El celular debe tener exactamente 9 dígitos numéricos.";
}

// ============================================
// 2️⃣ SI NO HAY ERRORES, ACTUALIZAR
// ============================================

if (empty($response['errors'])) {

    try {
        // Preparar consulta segura
        $sql = "UPDATE alumno SET nombre = ?, dni = ?, email = ?, celular = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $conn->error);
        }

        $stmt->bind_param("ssssi", $nombre, $dni, $email, $celular, $id);

        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }

        // Verificar que se actualizó correctamente
        if ($stmt->affected_rows >= 0) { // >= 0 porque si no hay cambios, affected_rows = 0

            // Log de auditoría
            error_log("ALUMNO EDITADO - ID: $id - Nombre: $nombre - DNI: $dni");

            $response['success'] = true;
            $response['message'] = "Alumno actualizado exitosamente.";

            $stmt->close();
        } else {
            throw new Exception("No se pudo actualizar el registro.");
        }
    } catch (Exception $e) {
        // Manejo de excepciones
        error_log("ERROR AL EDITAR ALUMNO: " . $e->getMessage());
        $response['message'] = "Error del sistema: No se pudo actualizar el alumno. Por favor, contacta al administrador.";
    }
} else {
    $response['message'] = "Por favor, corrige los errores en el formulario.";
}

$conn->close();

// Enviar respuesta JSON
echo json_encode($response);
exit;
