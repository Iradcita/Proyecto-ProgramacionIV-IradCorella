<?php

require dirname(__DIR__) . '/config/config.php';

$controlador = new AuthController();
$controlador->procesarLogout();
