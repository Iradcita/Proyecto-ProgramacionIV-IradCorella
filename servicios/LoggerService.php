<?php

// Servicio simple para registrar errores internos sin mostrarlos al usuario.
class LoggerService
{
    // Guarda una linea de log con fecha, nivel, contexto y mensaje.
    public static function registrar($nivel, $contexto, $mensaje)
    {
        $directorio = BASE_PATH . '/logs';

        if (!is_dir($directorio)) {
            mkdir($directorio, 0775, true);
        }

        $linea = '[' . date('Y-m-d H:i:s') . '] '
            . strtoupper($nivel) . ' '
            . '[' . $contexto . '] '
            . $mensaje . PHP_EOL;

        file_put_contents($directorio . '/app.log', $linea, FILE_APPEND);
    }
}
