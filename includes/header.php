<?php
// includes/header.php
// Recibe (opcional) $pageTitle para el título dinámico.
if (!isset($pageTitle)) $pageTitle = 'Panel';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= htmlspecialchars($pageTitle) ?> - Mi Admin</title>

    <!-- Enlaces CSS (ajusta rutas si es necesario) -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">


    <!-- Agrega aquí tus CSS personalizados -->
</head>
<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

    <?php
    // Aquí no cerramos wrapper: sidebar se incluirá después.
    // También puedes iniciar sesión o proteger la página aquí si quieres.
    ?>
    
