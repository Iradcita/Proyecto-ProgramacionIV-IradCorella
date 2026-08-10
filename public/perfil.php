<?php

// Ruta publica para ver y actualizar el perfil del usuario conectado.
require dirname(__DIR__) . '/config/config.php';

$controlador = new ClienteController();
$accion = $_POST['accion'] ?? 'mostrar';

if ($accion === 'datos') {
    $controlador->actualizarPerfil();
} elseif ($accion === 'password') {
    $controlador->cambiarPassword();
} elseif ($accion === 'foto') {
    $controlador->guardarFoto();
} else {
    $controlador->mostrarPerfil();
}
