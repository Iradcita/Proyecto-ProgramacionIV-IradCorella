<?php

// Ruta publica para buscar hoteles disponibles.
require dirname(__DIR__) . '/config/config.php';

$controlador = new ClienteController();
$controlador->hoteles();
