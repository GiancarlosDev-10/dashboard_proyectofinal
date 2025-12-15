<?php

/**
 * EDITAR CURSO
 * Con validaciones server-side completas y respuesta JSON para AJAX
 */

include("../../db.php");

// Configurar respuesta JSON
header('Content-Type: application/json');

// Inicializar respuesta
$response = [
    'success' => false,
    'errors'  => [],
    'message' => ''
];

// ==============================
// VALIDAR ID
// ==============================

if (empty($_POST['id'])) {
    $response['message'] = "ID de curso no proporcionado.";
    echo json_encode($response);
    exit;
}

$id = intval($_POST['id']);

// ==============================
// SANITIZAR DATOS
// ==============================

$nombre        = trim($_POST['nombre'] ?? '');
$categoria_id  = intval($_POST['categoria_id'] ?? 0);
$modalidad_id  = intval($_POST['modalidad_id'] ?? 0);
$fecha_inicio  = trim($_POST['fecha_inicio'] ?? '');
$cupos         = intval($_POST['cupos'] ?? 0);
$precio_raw    = str_replace(',', '.', trim($_POST['precio'] ?? ''));
$precio        = is_numeric($precio_raw) ? floatval($precio_raw) : null;
$estado        = $_POST['estado'] ?? 'Activo';

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
// ACTUALIZAR SI NO HAY ERRORES
// ==============================

if (empty($response['errors'])) {

    try {

        $sql = "UPDATE curso SET
                    nombre = ?,
                    categoria_id = ?,
                    modalidad_id = ?,
                    fecha_inicio = ?,
                    cupos = ?,
                    precio = ?,
                    estado = ?
                WHERE id = ?";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param(
            "siisidsi",
            $nombre,
            $categoria_id,
            $modalidad_id,
            $fecha_inicio,
            $cupos,
            $precio,
            $estado,
            $id
        );

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        // affected_rows puede ser 0 si no hubo cambios
        $response['success'] = true;
        $response['message'] = "Curso actualizado exitosamente.";

        error_log("CURSO EDITADO - ID: $id - Nombre: $nombre");

        $stmt->close();
    } catch (Exception $e) {

        error_log("ERROR AL EDITAR CURSO ID $id: " . $e->getMessage());
        $response['message'] = "Error del sistema: No se pudo actualizar el curso.";
    }
} else {
    $response['message'] = "Por favor corrige los errores del formulario.";
}

$conn->close();

// Enviar respuesta JSON
echo json_encode($response);
exit;
