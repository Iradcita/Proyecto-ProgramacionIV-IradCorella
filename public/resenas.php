<?php

// Ruta publica para guardar resenas de destinos desde cliente.
require dirname(__DIR__) . '/config/config.php';

$controlador = new ClienteController();
$controlador->guardarResena();
