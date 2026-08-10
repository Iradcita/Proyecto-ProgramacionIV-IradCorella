<?php

// Ruta publica para ver el historial de reservaciones del cliente.
require dirname(__DIR__) . '/config/config.php';

$controlador = new ClienteController();
$controlador->misReservaciones();
