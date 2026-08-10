<?php

// Ruta publica del modulo administrativo de reservaciones.
require dirname(__DIR__) . '/config/config.php';

$controlador = new AdminReservacionController();
$controlador->manejar();
