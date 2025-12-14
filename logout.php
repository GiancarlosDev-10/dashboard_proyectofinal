<?php

/**
 * LOGOUT - Cierre de sesión seguro
 * Destruye la sesión y redirige al login
 */

session_start();

// 1️⃣ Log de auditoría (opcional pero recomendado)
if (isset($_SESSION['admin_email'])) {
    $usuario = $_SESSION['admin_email'];
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'IP desconocida';
    $fecha = date('Y-m-d H:i:s');
    error_log("LOGOUT - Usuario: $usuario - IP: $ip - Fecha: $fecha");
}

// 2️⃣ Limpiar todas las variables de sesión
$_SESSION = array();

// 3️⃣ Destruir la cookie de sesión del navegador
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),           // Nombre de la cookie de sesión
        '',                       // Valor vacío
        time() - 42000,          // Tiempo pasado (expira la cookie)
        $params["path"],         // Ruta
        $params["domain"],       // Dominio
        $params["secure"],       // Solo HTTPS
        $params["httponly"]      // Solo HTTP (no JavaScript)
    );
}

// 4️⃣ Destruir la sesión completamente
session_destroy();

// 5️⃣ Eliminar cookie "Recordarme" si existe
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/', '', true, true);
}

// 6️⃣ Prevenir caché de esta página
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// 7️⃣ Redirigir al login
header("Location: index.php");
exit;
