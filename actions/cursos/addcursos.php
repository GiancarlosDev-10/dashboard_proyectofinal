<?php

/**
 * AGREGAR CURSO
 * Validaciones server-side + respuesta JSON (AJAX)
 */
session_start();
require '../../includes/csrf.php';
include("../../db.php");

// VALIDAR CSRF PRIMERO
verificar_csrf_o_morir();

header('Content-Type: application/json');

$response = [
    'success' => false,
    'errors'  => [],
    'message' => ''
];

// ==============================
// SANITIZAR Y OBTENER DATOS
// ==============================

$nombre        = trim($_POST['nombre'] ?? '');
$categoria_id  = intval($_POST['categoria_id'] ?? 0);
$modalidad_id  = intval($_POST['modalidad_id'] ?? 0);
$fecha_inicio  = trim($_POST['fecha_inicio'] ?? '');
$cupos         = intval($_POST['cupos'] ?? 0);
$precio_raw    = str_replace(',', '.', trim($_POST['precio'] ?? ''));
$precio        = is_numeric($precio_raw) ? floatval($precio_raw) : null;
$estado        = ($_POST['estado'] ?? 'Activo');

// ==============================
// VALIDACIONES SERVER-SIDE
// ==============================

// Nombre
if ($nombre === '') {
    $response['errors']['nombre'] = "El nombre del curso es obligatorio.";
} elseif (strlen($nombre) < 3) {
    $response['errors']['nombre'] = "El nombre debe tener al menos 3 caracteres.";
}

// Categoría
if ($categoria_id <= 0) {
    $response['errors']['categoria_id'] = "Debe seleccionar una categoría válida.";
}

// Modalidad
if ($modalidad_id <= 0) {
    $response['errors']['modalidad_id'] = "Debe seleccionar una modalidad válida.";
}

// Fecha
if ($fecha_inicio === '') {
    $response['errors']['fecha_inicio'] = "La fecha de inicio es obligatoria.";
} elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_inicio) || strtotime($fecha_inicio) === false) {
    $response['errors']['fecha_inicio'] = "Formato de fecha inválido (YYYY-MM-DD).";
}

// Cupos
if ($cupos <= 0) {
    $response['errors']['cupos'] = "Los cupos deben ser mayores a 0.";
}

// Precio
if ($precio === null || $precio < 0) {
    $response['errors']['precio'] = "El precio debe ser un valor numérico válido.";
}

// Estado
if (!in_array($estado, ['Activo', 'Inactivo'])) {
    $response['errors']['estado'] = "Estado inválido.";
}

// ==============================
// INSERTAR SI NO HAY ERRORES
// ==============================

if (empty($response['errors'])) {

    try {

        $sql = "INSERT INTO curso 
                (nombre, categoria_id, modalidad_id, fecha_inicio, cupos, precio, estado)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param(
            "siisids",
            $nombre,
            $categoria_id,
            $modalidad_id,
            $fecha_inicio,
            $cupos,
            $precio,
            $estado
        );

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        if ($stmt->affected_rows > 0) {

            $response['success'] = true;
            $response['message'] = "Curso registrado exitosamente.";
            $response['curso_id'] = $stmt->insert_id;

            error_log("CURSO AGREGADO - ID: {$stmt->insert_id} - Nombre: $nombre");
        } else {
            throw new Exception("No se pudo insertar el curso.");
        }

        $stmt->close();
    } catch (Exception $e) {

        error_log("ERROR ADD CURSO: " . $e->getMessage());
        $response['message'] = "Error del sistema. No se pudo registrar el curso.";
    }
} else {
    $response['message'] = "Por favor corrige los errores del formulario.";
}

$conn->close();

echo json_encode($response);
exit;
