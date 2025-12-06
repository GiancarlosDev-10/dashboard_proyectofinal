<?php
include("../../db.php");

$errores = [];

if (!empty($_POST['id'])) {
    $id = $_POST['id'];
    $nombre = trim($_POST['nombre'] ?? '');
    $dni = trim($_POST['dni'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $celular = trim($_POST['celular'] ?? '');

    if ($nombre === '') $errores[] = "El nombre es obligatorio.";
    if ($dni === '') $errores[] = "El DNI es obligatorio.";
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errores[] = "El email es obligatorio y debe ser válido.";
    if ($celular === '') $errores[] = "El celular es obligatorio.";

    if (empty($errores)) {
        $sql = "UPDATE alumno SET nombre = ?, dni = ?, email = ?, celular = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $nombre, $dni, $email, $celular, $id);
        $stmt->execute();
        header("Location: addalumno.php?edit=ok");
        exit;
    } else {
        $params = http_build_query([
            'edit_error' => implode('|', $errores),
            'id' => $id,
            'nombre' => $nombre,
            'dni' => $dni,
            'email' => $email,
            'celular' => $celular
        ]);
        header("Location: addalumno.php?$params#editModal");
        exit;
    }
}

header("Location: addalumno.php");
exit;
