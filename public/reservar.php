<?php

// Ruta publica para crear reservaciones de clientes.
require dirname(__DIR__) . '/config/config.php';

$controlador = new ClienteController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controlador->guardarReservacion();
} else {
    $controlador->mostrarReservar();
}
