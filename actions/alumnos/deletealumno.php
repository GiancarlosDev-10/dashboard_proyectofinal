<?php

/**
 * ELIMINAR ALUMNO
 * Con validaciones completas, verificación de relaciones y respuesta JSON
 */

include("../../db.php");

// Configurar respuesta JSON
header('Content-Type: application/json');

// Inicializar array de respuesta
$response = [
    'success' => false,
    'message' => '',
    'alumno' => null
];

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = "Método no permitido. Use POST.";
    echo json_encode($response);
    exit;
}

// Obtener y validar ID
$id = intval(trim($_POST['id'] ?? ''));

if ($id <= 0) {
    $response['message'] = "ID inválido.";
    echo json_encode($response);
    exit;
}

try {
    // ============================================
    // 1️⃣ VERIFICAR EXISTENCIA Y OBTENER DATOS
    // ============================================

    $stmt_check = $conn->prepare("SELECT id, nombre, dni, email FROM alumno WHERE id = ?");
    $stmt_check->bind_param("i", $id);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows === 0) {
        $response['message'] = "No se encontró el alumno con ID $id.";
        $stmt_check->close();
        $conn->close();
        echo json_encode($response);
        exit;
    }

    $alumno = $result->fetch_assoc();
    $stmt_check->close();

    // ============================================
    // 2️⃣ VERIFICAR RELACIONES CON OTRAS TABLAS
    // ============================================
    // Aquí puedes agregar verificaciones según tu BD
    // Ejemplo: Si tienes tabla "matriculas"

    /*
    $stmt_matriculas = $conn->prepare("SELECT COUNT(*) as total FROM matriculas WHERE alumno_id = ?");
    $stmt_matriculas->bind_param("i", $id);
    $stmt_matriculas->execute();
    $matriculas = $stmt_matriculas->get_result()->fetch_assoc();
    $stmt_matriculas->close();
    
    if ($matriculas['total'] > 0) {
        $response['message'] = "No se puede eliminar. El alumno tiene {$matriculas['total']} matrícula(s) activa(s).";
        $conn->close();
        echo json_encode($response);
        exit;
    }
    */

    // ============================================
    // 3️⃣ ELIMINAR ALUMNO
    // ============================================

    $stmt_delete = $conn->prepare("DELETE FROM alumno WHERE id = ?");
    $stmt_delete->bind_param("i", $id);
    $stmt_delete->execute();

    // Verificar que se eliminó correctamente
    if ($stmt_delete->affected_rows > 0) {

        // Log de auditoría
        error_log("ALUMNO ELIMINADO - ID: {$alumno['id']} - Nombre: {$alumno['nombre']} - DNI: {$alumno['dni']}");

        $response['success'] = true;
        $response['message'] = "Alumno '{$alumno['nombre']}' (DNI: {$alumno['dni']}) eliminado exitosamente.";
        $response['alumno'] = $alumno;
    } else {
        $response['message'] = "No se pudo eliminar el alumno. El registro pudo haber sido eliminado previamente.";
    }

    $stmt_delete->close();
} catch (Exception $e) {
    // Manejo de excepciones
    error_log("ERROR AL ELIMINAR ALUMNO ID $id: " . $e->getMessage());

    // Detectar error de clave foránea
    if (
        strpos($e->getMessage(), 'foreign key constraint') !== false ||
        strpos($e->getMessage(), 'FOREIGN KEY') !== false
    ) {
        $response['message'] = "No se puede eliminar. El alumno tiene registros relacionados (matrículas, notas, etc.).";
    } else {
        $response['message'] = "Error del sistema: No se pudo eliminar el alumno. Por favor, contacta al administrador.";
    }
}

$conn->close();

// Enviar respuesta JSON
echo json_encode($response);
exit;
