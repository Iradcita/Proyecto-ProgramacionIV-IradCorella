<?php

// Ruta publica del modulo administrativo de hoteles.
require dirname(__DIR__) . '/config/config.php';

$controlador = new AdminHotelController();
$controlador->manejar();
