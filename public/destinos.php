<?php

// Ruta publica para consultar destinos como cliente.
require dirname(__DIR__) . '/config/config.php';

$controlador = new ClienteController();

if (isset($_GET['accion']) && $_GET['accion'] === 'detalle') {
    $controlador->detalleDestino();
} else {
    $controlador->destinos();
}
