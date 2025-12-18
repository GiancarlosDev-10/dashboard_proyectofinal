<?php
session_start();
header('Content-Type: application/json');

// Verificar que el usuario esté logueado y sea admin
if (!isset($_SESSION['admin_id']) || ($_SESSION['admin_rol'] ?? 'alumno') !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'No tienes permisos para realizar esta acción']);
    exit;
}

include("../../db.php");

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = intval($_POST['id'] ?? 0);

    if ($id > 0) {
        try {
            // Actualizar estado a "Pagado"
            $sql = "UPDATE pagos SET estado = 'Pagado' WHERE id = ?";
            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                throw new Exception("Error en la consulta: " . $conn->error);
            }

            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $response['success'] = true;
                    $response['message'] = 'Estado actualizado correctamente';
                } else {
                    $response['message'] = 'No se encontró el registro o ya estaba marcado como pagado';
                }
            } else {
                $response['message'] = 'Error al ejecutar la actualización';
            }

            $stmt->close();
        } catch (Exception $e) {
            error_log("Error al marcar como pagado: " . $e->getMessage());
            $response['message'] = 'Error al procesar la solicitud';
        }
    } else {
        $response['message'] = 'ID inválido';
    }
} else {
    $response['message'] = 'Método no permitido';
}

$conn->close();
echo json_encode($response);
