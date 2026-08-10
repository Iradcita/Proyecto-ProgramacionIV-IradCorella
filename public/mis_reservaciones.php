<?php

// Ruta publica para ver el historial de reservaciones del cliente.
require dirname(__DIR__) . '/config/config.php';

$controlador = new ClienteController();

if (isset($_GET['accion']) && $_GET['accion'] === 'detalle') {
    $controlador->detalleReservacion();
} else {
    $controlador->misReservaciones();
}
