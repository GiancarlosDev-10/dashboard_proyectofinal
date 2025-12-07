<?php
include("../../db.php");

$errores = [];

$nombre = trim($_POST['nombre'] ?? '');
$dni = trim($_POST['dni'] ?? '');
$email = trim($_POST['email'] ?? '');
$celular = trim($_POST['celular'] ?? '');

// Validaciones
if ($nombre === '') $errores[] = "El nombre es obligatorio.";
if ($dni === '') $errores[] = "El DNI es obligatorio.";
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL))
    $errores[] = "El email es obligatorio y debe ser válido.";
if ($celular === '') $errores[] = "El celular es obligatorio.";

if (empty($errores)) {

    // Insert seguro
    $sql = "INSERT INTO alumno (nombre, dni, email, celular) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nombre, $dni, $email, $celular);
    $stmt->execute();

    file_put_contents("debug_add.txt", "ERROR SQL: " . $stmt->error . "\n", FILE_APPEND);
    file_put_contents("debug_add.txt", "AFECTADAS: " . $stmt->affected_rows . "\n", FILE_APPEND);


    // Redirigir con éxito
    header("Location: indexalumno.php?add=ok");
    exit;
} else {

    // Redirigir con errores y devolver datos al modal
    $params = http_build_query([
        'add_error' => implode('|', $errores),
        'nombre' => $nombre,
        'dni' => $dni,
        'email' => $email,
        'celular' => $celular
    ]);

    header("Location: indexalumno.php?$params#addModal");
    exit;
}
