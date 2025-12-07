<?php
include("../../db.php");

$errores = [];

// Recibir datos del formulario
$nombre       = trim($_POST['nombre'] ?? '');
$especialidad = trim($_POST['especialidad'] ?? '');
$dni          = trim($_POST['dni'] ?? '');

// Validaciones
if ($nombre === '')          $errores[] = "El nombre es obligatorio.";
if ($especialidad === '')    $errores[] = "La especialidad es obligatoria.";
if ($dni === '' || !ctype_digit($dni))
    $errores[] = "El DNI es obligatorio y debe contener solo números.";

// SI NO HAY ERRORES → INSERTAR
if (empty($errores)) {

    $sql = "INSERT INTO docente (nombre, especialidad, dni) VALUES (?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $nombre, $especialidad, $dni);
    $stmt->execute();

    // Redirigir con éxito
    header("Location: indexdocentes.php?add=ok");
    exit;
} else {

    // Redirigir con errores
    $params = http_build_query([
        'error' => implode('|', $errores)
    ]);

    header("Location: indexdocentes.php?$params#addModal");
    exit;
}
