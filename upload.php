<?php
$result = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['uploaded_file']) && $_FILES['uploaded_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['uploaded_file']['tmp_name'];
        $fileName = basename($_FILES['uploaded_file']['name']);

        $uploadFolder = 'includes/uploads/';
        if (!is_dir($uploadFolder)) {
            mkdir($uploadFolder, 0755, true);
        }

        $destination = $uploadFolder . $fileName;

        if (move_uploaded_file($fileTmpPath, $destination)) {
            $result = "Archivo subido: " . htmlspecialchars($fileName, ENT_QUOTES, 'UTF-8');
        } else {
            $result = "Error al mover el archivo cargado.";
        }
    } else {
        $result = "Ocurrió un error en la subida o no se envió archivo.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Resultado de subida</title>
</head>

<body>
    <h2><?php echo $result; ?></h2>
    <a href="index.html">Volver</a>
</body>

</html>