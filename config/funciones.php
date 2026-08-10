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

// Indica si el usuario conectado tiene el rol de Administrador.
function esAdministrador()
{
    return usuarioAutenticado() && isset($_SESSION['usuario']['id_rol']) && (int) $_SESSION['usuario']['id_rol'] === 1;
}

// Indica si el usuario conectado tiene el rol de Cliente.
function esCliente()
{
    return usuarioAutenticado() && isset($_SESSION['usuario']['id_rol']) && (int) $_SESSION['usuario']['id_rol'] === 2;
}

// Obliga a que exista una sesion iniciada antes de entrar a una pagina privada.
function exigirAutenticacion()
{
    if (!usuarioAutenticado()) {
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

// Protege las pantallas administrativas para que solo entre el rol Administrador.
function exigirAdministrador()
{
    exigirAutenticacion();

    if (!esAdministrador()) {
        guardarMensaje('error', 'No tienes permisos para ingresar a administracion.');
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }
}

// Protege modulos de cliente para evitar acceso cruzado desde administracion.
function exigirCliente()
{
    exigirAutenticacion();

    if (!esCliente()) {
        guardarMensaje('error', 'No tienes permisos para ingresar a este modulo.');
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }
}

// Registra una accion en bitacora sin romper el flujo si falla el log.
function registrarBitacora($accion, $tablaAfectada = null, $idRegistroAfectado = null)
{
    try {
        $idUsuario = obtenerEntero($_SESSION['usuario']['id_usuario'] ?? 0);
        Bitacora::registrar($idUsuario, $accion, $tablaAfectada, $idRegistroAfectado);
    } catch (Exception $e) {
        LoggerService::registrar('error', 'bitacora', $e->getMessage());
    }
}

// Registra excepciones internas con contexto del modulo.
function registrarExcepcion($contexto, $excepcion)
{
    LoggerService::registrar('error', $contexto, $excepcion->getMessage());
}

// Convierte valores recibidos del formulario o URL a enteros seguros.
function obtenerEntero($valor, $porDefecto = 0)
{
    return filter_var($valor, FILTER_VALIDATE_INT) !== false ? (int) $valor : $porDefecto;
}

function cerrarSesion()
{
    $_SESSION = [];
    session_destroy();
}
