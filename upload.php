<?php
require __DIR__ . '/db.php';

$result = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;
    $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : null;
    $fotoFilename = null;

    if (isset($_FILES['uploaded_file']) && $_FILES['uploaded_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['uploaded_file']['tmp_name'];
        $origName = $_FILES['uploaded_file']['name'];
        $ext = pathinfo($origName, PATHINFO_EXTENSION);
        $baseName = pathinfo($origName, PATHINFO_FILENAME);
        $safeBase = preg_replace('/[^A-Za-z0-9_\-]/', '_', $baseName);
        $newName = $safeBase . '_' . time() . '.' . $ext;

        $uploadFolder = 'img/fotos/';
        if (!is_dir($uploadFolder)) {
            mkdir($uploadFolder, 0755, true);
        }

        $destination = $uploadFolder . $newName;

        if (move_uploaded_file($fileTmpPath, $destination)) {
            $fotoFilename = $newName;
            $result = "Archivo subido: " . htmlspecialchars($newName, ENT_QUOTES, 'UTF-8');
        } else {
            $result = "Error al mover el archivo cargado.";
        }
    }

    if (($descripcion !== null || $fotoFilename !== null) && $id) {
        $updates = [];
        $params = [];
        $types = '';

        if ($descripcion !== null) {
            $updates[] = 'descripcion = ?';
            $params[] = $descripcion;
            $types .= 's';
        }
        if ($fotoFilename !== null) {
            $updates[] = 'foto = ?';
            $params[] = $fotoFilename;
            $types .= 's';
        }

        $params[] = $id;
        $types .= 'i';

        if (count($updates) > 0) {
            $sql = "UPDATE admin SET " . implode(', ', $updates) . " WHERE id = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param($types, ...$params);
                if ($stmt->execute()) {
                    $result .= " Datos actualizados.";
                } else {
                    $result .= " Error BD: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $result .= " Error al preparar la consulta: " . $conn->error;
            }
        }
    }
    if (!$result) $result = "No se detectaron cambios.";
}

// Si la petición es AJAX, responder JSON
$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

$response = [
    'success' => (strpos($result, 'Error') === false),
    'message' => $result,
    'foto' => $fotoFilename ?? null,
];

if ($isAjax) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response);
    exit;
}

// Si no es AJAX, mostrar una página simple (comportamiento previo)
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Resultado de subida</title>
    <style>body{font-family:Arial,Helvetica,sans-serif;padding:20px}</style>
</head>

<body>
    <h2><?php echo htmlspecialchars($response['message']); ?></h2>
    <a href="/admin_php/perfil.php">Volver al perfil</a>
</body>

</html>