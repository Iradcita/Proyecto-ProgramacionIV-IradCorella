<?php

// Ruta publica para buscar actividades turisticas.
require dirname(__DIR__) . '/config/config.php';

$controlador = new ClienteController();
$controlador->actividades();
