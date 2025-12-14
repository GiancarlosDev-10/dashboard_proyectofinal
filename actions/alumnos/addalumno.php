<?php

/**
 * AGREGAR ALUMNO
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

// Sanitizar y obtener datos del POST
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
    // Verificar si el DNI ya existe
    try {
        $stmt_check = $conn->prepare("SELECT id FROM alumno WHERE dni = ?");
        $stmt_check->bind_param("s", $dni);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows > 0) {
            $response['errors']['dni'] = "El DNI ya está registrado en el sistema.";
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
    // Verificar si el email ya existe
    try {
        $stmt_check = $conn->prepare("SELECT id FROM alumno WHERE email = ?");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows > 0) {
            $response['errors']['email'] = "El email ya está registrado en el sistema.";
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
// 2️⃣ SI NO HAY ERRORES, INSERTAR
// ============================================

if (empty($response['errors'])) {

    try {
        // Preparar consulta segura
        $sql = "INSERT INTO alumno (nombre, dni, email, celular) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $conn->error);
        }

        $stmt->bind_param("ssss", $nombre, $dni, $email, $celular);

        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }

        // Verificar que se insertó correctamente
        if ($stmt->affected_rows > 0) {
            $nuevo_id = $stmt->insert_id;

            // Log de auditoría (opcional pero recomendado)
            error_log("ALUMNO AGREGADO - ID: $nuevo_id - Nombre: $nombre - DNI: $dni");

            $response['success'] = true;
            $response['message'] = "Alumno registrado exitosamente.";
            $response['alumno_id'] = $nuevo_id;

            $stmt->close();
        } else {
            throw new Exception("No se pudo insertar el registro.");
        }
    } catch (Exception $e) {
        // Manejo de excepciones
        error_log("ERROR AL AGREGAR ALUMNO: " . $e->getMessage());
        $response['message'] = "Error del sistema: No se pudo registrar el alumno. Por favor, contacta al administrador.";
    }
} else {
    $response['message'] = "Por favor, corrige los errores en el formulario.";
}

$conn->close();

// Enviar respuesta JSON
echo json_encode($response);
exit;
