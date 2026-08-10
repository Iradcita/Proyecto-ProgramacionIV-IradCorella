<?php

// Ruta publica del modulo administrativo de destinos.
require dirname(__DIR__) . '/config/config.php';

$controlador = new AdminDestinoController();
$controlador->manejar();
