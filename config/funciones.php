<?php

// Funciones de apoyo que se usan en varias partes del sistema
// (token contra CSRF, mensajes de una sola vez y datos de sesion).

function generarTokenCsrf()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function validarTokenCsrf($token)
{
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $token);
}

function guardarMensaje($tipo, $texto)
{
    $_SESSION['mensajes'][$tipo][] = $texto;
}

function obtenerMensajes()
{
    $mensajes = isset($_SESSION['mensajes']) ? $_SESSION['mensajes'] : [];
    unset($_SESSION['mensajes']);

    return $mensajes;
}

function usuarioAutenticado()
{
    return !empty($_SESSION['usuario']);
}

function cerrarSesion()
{
    $_SESSION = [];
    session_destroy();
}
