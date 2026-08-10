<?php

// Ruta publica del modulo administrativo de actividades.
require dirname(__DIR__) . '/config/config.php';

$controlador = new AdminActividadController();
$controlador->manejar();
