<?php

/**
 * EDITAR DOCENTE
 * Validaciones server-side completas y respuesta JSON para AJAX
 * Usa especialidad como texto libre con validación anti-duplicados
 * SINCRONIZA automáticamente con la tabla categoria
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
// FUNCIÓN: NORMALIZAR TEXTO
// ==============================
function normalizar_texto($texto)
{
    $texto = strtolower($texto);
    $texto = preg_replace('/\s+/', ' ', $texto);
    $texto = trim($texto);

    $tildes = ['á', 'é', 'í', 'ó', 'ú', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ'];
    $sin_tildes = ['a', 'e', 'i', 'o', 'u', 'n', 'a', 'e', 'i', 'o', 'u', 'n'];
    $texto = str_replace($tildes, $sin_tildes, $texto);

    return $texto;
}

// ==============================
// VALIDAR ID
// ==============================

if (empty($_POST['id'])) {
    $response['message'] = "ID de docente no proporcionado.";
    echo json_encode($response);
    exit;
}

$id = intval($_POST['id']);

if ($id <= 0) {
    $response['message'] = "ID de docente inválido.";
    echo json_encode($response);
    exit;
}

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
} elseif (strlen($nombre) > 100) {
    $response['errors']['nombre'] = "El nombre no puede superar 100 caracteres.";
}

// Especialidad con verificación inteligente de duplicados
$especialidad_final = $especialidad;

if ($especialidad === '') {
    $response['errors']['especialidad'] = "La especialidad es obligatoria.";
} elseif (strlen($especialidad) < 3) {
    $response['errors']['especialidad'] = "La especialidad debe tener al menos 3 caracteres.";
} else {
    // Verificar si ya existe una especialidad similar (normalizada)
    try {
        $esp_normalizada = normalizar_texto($especialidad);

        $stmt_check_esp = $conn->prepare("SELECT DISTINCT especialidad FROM docente WHERE especialidad IS NOT NULL");
        $stmt_check_esp->execute();
        $result_esp = $stmt_check_esp->get_result();

        $especialidad_existente = null;

        while ($esp = $result_esp->fetch_assoc()) {
            if (normalizar_texto($esp['especialidad']) === $esp_normalizada) {
                $especialidad_existente = $esp['especialidad'];
                break;
            }
        }

        $stmt_check_esp->close();

        if ($especialidad_existente && $especialidad_existente !== $especialidad) {
            // Existe una especialidad similar, usar esa (con su capitalización original)
            $especialidad_final = $especialidad_existente;
            error_log("ESPECIALIDAD SIMILAR DETECTADA (EDIT): '$especialidad' → usando '$especialidad_existente'");
        }
    } catch (Exception $e) {
        error_log("ERROR AL VERIFICAR ESPECIALIDAD (EDIT): " . $e->getMessage());
        // No es crítico, continuar con la especialidad ingresada
    }
}

// DNI
if ($dni === '') {
    $response['errors']['dni'] = "El DNI es obligatorio.";
} elseif (!preg_match('/^[0-9]{8}$/', $dni)) {
    $response['errors']['dni'] = "El DNI debe tener exactamente 8 dígitos numéricos.";
} else {
    // Verificar duplicado en otro docente (excluyendo el actual)
    try {
        $stmt_check_dni = $conn->prepare(
            "SELECT id FROM docente WHERE dni = ? AND id != ?"
        );
        $stmt_check_dni->bind_param("si", $dni, $id);
        $stmt_check_dni->execute();
        $result_dni = $stmt_check_dni->get_result();

        if ($result_dni->num_rows > 0) {
            $response['errors']['dni'] = "El DNI ya está registrado en otro docente.";
        }
        $stmt_check_dni->close();
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

        // ========================================
        // SINCRONIZAR CON TABLA CATEGORIA
        // ========================================
        // Verificar si la especialidad existe en la tabla categoria
        $categoria_existe = false;
        $esp_normalizada = normalizar_texto($especialidad_final);

        $stmt_check_cat = $conn->prepare("SELECT id, nombre FROM categoria");
        $stmt_check_cat->execute();
        $result_cat = $stmt_check_cat->get_result();

        while ($cat = $result_cat->fetch_assoc()) {
            if (normalizar_texto($cat['nombre']) === $esp_normalizada) {
                $categoria_existe = true;
                break;
            }
        }

        $stmt_check_cat->close();

        // Si NO existe en categoria, crearla
        if (!$categoria_existe) {
            $stmt_insert_cat = $conn->prepare("INSERT INTO categoria (nombre) VALUES (?)");
            $stmt_insert_cat->bind_param("s", $especialidad_final);

            if ($stmt_insert_cat->execute()) {
                error_log("CATEGORÍA SINCRONIZADA (EDIT): Nueva categoría '$especialidad_final' agregada a tabla categoria (ID: {$stmt_insert_cat->insert_id})");
            } else {
                error_log("ADVERTENCIA: No se pudo sincronizar la categoría '$especialidad_final' con tabla categoria");
            }

            $stmt_insert_cat->close();
        }

        // ========================================
        // ACTUALIZAR DOCENTE
        // ========================================

        $sql = "UPDATE docente
                SET nombre = ?, especialidad = ?, dni = ?
                WHERE id = ?";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("sssi", $nombre, $especialidad_final, $dni, $id);

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        // affected_rows puede ser 0 si no hubo cambios
        $response['success'] = true;
        $response['message'] = "Docente actualizado exitosamente.";

        error_log("DOCENTE EDITADO - ID: $id - Nombre: $nombre - DNI: $dni - Especialidad: $especialidad_final");

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
