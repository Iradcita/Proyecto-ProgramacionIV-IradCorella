<?php

// Ruta publica del modulo administrativo de bitacora.
require dirname(__DIR__) . '/config/config.php';

$controlador = new AdminBitacoraController();
$controlador->mostrar();
