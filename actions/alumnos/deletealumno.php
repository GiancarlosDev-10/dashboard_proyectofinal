<?php
include("../../db.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Eliminar el registro con el ID recibido
    $conn->query("DELETE FROM alumno WHERE id = $id");
}

// Redirigir de nuevo a la página principal
header("Location: addalumno.php");
exit;
