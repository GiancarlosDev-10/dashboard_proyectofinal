<?php
include("../../db.php");

$errores = [];

if (!empty($_POST['id'])) {

    $id            = intval(trim($_POST['id']));
    $nombre        = trim($_POST['nombre'] ?? '');
    $categoria_id  = trim($_POST['categoria_id'] ?? '');
    $modalidad_id  = trim($_POST['modalidad_id'] ?? '');
    $fecha_inicio  = trim($_POST['fecha_inicio'] ?? '');
    $cupos         = trim($_POST['cupos'] ?? '');
    $precio        = trim($_POST['precio'] ?? '');
    $estado        = trim($_POST['estado'] ?? '');

    // VALIDACIONES
    if ($nombre === '')
        $errores[] = "El nombre del curso es obligatorio.";

    if ($categoria_id === '' || !is_numeric($categoria_id))
        $errores[] = "Debe seleccionar una categoría.";

    if ($modalidad_id === '' || !is_numeric($modalidad_id))
        $errores[] = "Debe seleccionar una modalidad.";

    if ($fecha_inicio === '')
        $errores[] = "La fecha de inicio es obligatoria.";

    if ($cupos === '' || !is_numeric($cupos))
        $errores[] = "Los cupos deben ser un número.";

    if ($precio === '' || !is_numeric($precio))
        $errores[] = "El precio debe ser numérico.";

    if ($estado === '')
        $errores[] = "Debe seleccionar un estado.";

    // SI NO HAY ERRORES → UPDATE
    if (empty($errores)) {

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
        $stmt->bind_param(
            "siissisi",
            $nombre,
            $categoria_id,
            $modalidad_id,
            $fecha_inicio,
            $cupos,
            $precio,
            $estado,
            $id
        );

        $stmt->execute();

        header("Location: indexcursos.php?edit=ok");
        exit;
    } else {

        // DEVOLVER ERRORES AL MODAL
        $params = http_build_query([
            'error' => implode('|', $errores),
        ]);

        header("Location: indexcursos.php?$params#editModal");
        exit;
    }
}
