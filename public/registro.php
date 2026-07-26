<?php

require dirname(__DIR__) . '/config/config.php';

$controlador = new AuthController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controlador->procesarRegistro();
} else {
    $controlador->mostrarRegistro();
}
