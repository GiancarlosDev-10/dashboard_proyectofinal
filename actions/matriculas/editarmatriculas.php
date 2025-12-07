<?php
include("../../db.php");

$errores = [];

if (!empty($_POST['id'])) {

    $id               = intval(trim($_POST['id']));
    $curso_id         = trim($_POST['curso_id'] ?? '');
    $estado           = trim($_POST['estado'] ?? '');

    // Estos NO se modifican
    // $alumno_id (bloqueado en modal)
    // $fecha_inscripcion (solo lectura)

    // Validaciones
    if ($curso_id === '' || !is_numeric($curso_id)) {
        $errores[] = "Debe seleccionar un curso.";
    }

    if ($estado === '') {
        $errores[] = "Debe seleccionar un estado.";
    }

    // SI TODO ESTÁ CORRECTO → UPDATE
    if (empty($errores)) {

        $sql = "UPDATE matricula SET 
                    curso_id = ?, 
                    estado = ?
                WHERE id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "isi",
            $curso_id,
            $estado,
            $id
        );

        $stmt->execute();

        header("Location: indexmatriculas.php?edit=ok");
        exit;
    } else {

        // Enviar errores al modal
        $params = http_build_query([
            'error' => implode('|', $errores)
        ]);

        header("Location: indexmatriculas.php?$params#editModal");
        exit;
    }
}
