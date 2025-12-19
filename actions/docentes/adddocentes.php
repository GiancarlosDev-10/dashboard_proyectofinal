<?php

/**
 * AGREGAR DOCENTE
 * Validaciones server-side + respuesta JSON para AJAX
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
// SANITIZAR Y OBTENER DATOS
// ==============================

$nombre       = trim($_POST['nombre'] ?? '');
$especialidad = trim($_POST['especialidad'] ?? '');
$dni          = trim($_POST['dni'] ?? '');

// ==============================
// FUNCIÓN: NORMALIZAR TEXTO
// ==============================
// Elimina tildes, convierte a minúsculas y quita espacios extra
function normalizar_texto($texto)
{
    $texto = strtolower($texto);
    $texto = preg_replace('/\s+/', ' ', $texto); // Espacios múltiples → uno solo
    $texto = trim($texto);

    // Eliminar tildes
    $tildes = ['á', 'é', 'í', 'ó', 'ú', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ'];
    $sin_tildes = ['a', 'e', 'i', 'o', 'u', 'n', 'a', 'e', 'i', 'o', 'u', 'n'];
    $texto = str_replace($tildes, $sin_tildes, $texto);

    return $texto;
}

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
            error_log("ESPECIALIDAD SIMILAR DETECTADA: '$especialidad' → usando '$especialidad_existente'");
        }
    } catch (Exception $e) {
        error_log("ERROR AL VERIFICAR ESPECIALIDAD: " . $e->getMessage());
        // No es crítico, continuar con la especialidad ingresada
    }
}

// DNI
if ($dni === '') {
    $response['errors']['dni'] = "El DNI es obligatorio.";
} elseif (!preg_match('/^[0-9]{8}$/', $dni)) {
    $response['errors']['dni'] = "El DNI debe tener exactamente 8 dígitos numéricos.";
} else {
    // Verificar duplicado
    try {
        $stmt_check_dni = $conn->prepare("SELECT id FROM docente WHERE dni = ?");
        $stmt_check_dni->bind_param("s", $dni);
        $stmt_check_dni->execute();
        $result_dni = $stmt_check_dni->get_result();

        if ($result_dni->num_rows > 0) {
            $response['errors']['dni'] = "El DNI ya está registrado en otro docente.";
        }
        $stmt_check_dni->close();
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
                error_log("CATEGORÍA SINCRONIZADA: Nueva categoría '$especialidad_final' agregada a tabla categoria (ID: {$stmt_insert_cat->insert_id})");
            } else {
                error_log("ADVERTENCIA: No se pudo sincronizar la categoría '$especialidad_final' con tabla categoria");
            }

            $stmt_insert_cat->close();
        }

        // ========================================
        // INSERTAR DOCENTE
        // ========================================

        $sql = "INSERT INTO docente (nombre, especialidad, dni)
                VALUES (?, ?, ?)";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("sss", $nombre, $especialidad_final, $dni);

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        if ($stmt->affected_rows > 0) {

            $response['success'] = true;
            $response['message'] = "Docente registrado exitosamente.";
            $response['docente_id'] = $stmt->insert_id;

            error_log("DOCENTE AGREGADO - ID: {$stmt->insert_id} - Nombre: $nombre - DNI: $dni - Especialidad: $especialidad_final");
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
