<?php
include("../../db.php");

$errores = [];

if (!empty($_POST['id'])) {

    $id           = intval(trim($_POST['id']));
    $nombre       = trim($_POST['nombre'] ?? '');
    $especialidad = trim($_POST['especialidad'] ?? '');
    $dni          = trim($_POST['dni'] ?? '');

    // VALIDACIONES
    if ($nombre === '')          $errores[] = "El nombre es obligatorio.";
    if ($especialidad === '')    $errores[] = "La especialidad es obligatoria.";
    if ($dni === '' || !ctype_digit($dni))
        $errores[] = "El DNI debe contener solo números.";

    if (empty($errores)) {

        $sql = "UPDATE docente SET 
                    nombre = ?, 
                    especialidad = ?, 
                    dni = ?
                WHERE id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $nombre, $especialidad, $dni, $id);
        $stmt->execute();

        header("Location: /admin_php/actions/docentes/indexdocentes.php?edit=ok");
        exit;
    } else {

        $params = http_build_query([
            'error' => implode('|', $errores)
        ]);

        header("Location: /admin_php/actions/docentes/indexdocentes.php?$params#editModal");
        exit;
    }
}
