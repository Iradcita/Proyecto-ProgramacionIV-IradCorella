<?php

// Ruta publica del modulo administrativo de usuarios.
require dirname(__DIR__) . '/config/config.php';

$controlador = new AdminUsuarioController();
$controlador->manejar();
