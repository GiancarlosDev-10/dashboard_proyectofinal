<?php

/**
 * Sistema de protección CSRF (Cross-Site Request Forgery)
 * Genera y valida tokens de seguridad para formularios
 */

// ============================================
// GENERAR TOKEN CSRF
// ============================================

function generar_csrf_token()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// ============================================
// VALIDAR TOKEN CSRF
// ============================================

function validar_csrf_token($token)
{
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }

    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        return false;
    }

    return true;
}

// ============================================
// GENERAR INPUT HIDDEN CON TOKEN
// ============================================

function csrf_input()
{
    $token = generar_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

// ============================================
// VERIFICAR TOKEN O MORIR
// ============================================

function verificar_csrf_o_morir()
{
    if (!isset($_POST['csrf_token'])) {
        error_log("CSRF: Token no enviado - IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'desconocida'));
        die(json_encode([
            'success' => false,
            'message' => 'Token de seguridad no enviado. Recarga la página.'
        ]));
    }

    if (!validar_csrf_token($_POST['csrf_token'])) {
        error_log("CSRF: Token inválido - IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'desconocida'));
        die(json_encode([
            'success' => false,
            'message' => 'Token de seguridad inválido. Recarga la página.'
        ]));
    }
}
