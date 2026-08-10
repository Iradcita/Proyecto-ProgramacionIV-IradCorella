<?php

// Ruta publica del modulo administrativo de reportes.
require dirname(__DIR__) . '/config/config.php';

$controlador = new AdminReporteController();
$controlador->mostrar();
