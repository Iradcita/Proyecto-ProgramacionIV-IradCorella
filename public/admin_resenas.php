<?php

// Ruta publica del modulo administrativo de moderacion de resenas.
require dirname(__DIR__) . '/config/config.php';

$controlador = new AdminResenaController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controlador->cambiarEstado();
} else {
    $controlador->listar();
}
