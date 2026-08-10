<?php

// Servicio para consumir APIs REST publicas usadas por los reportes.
class ApiService
{
    // Consulta clima actual con Open-Meteo usando latitud y longitud.
    public function obtenerClima($latitud, $longitud)
    {
        $url = 'https://api.open-meteo.com/v1/forecast?latitude=' . urlencode($latitud)
            . '&longitude=' . urlencode($longitud)
            . '&current=temperature_2m,wind_speed_10m,relative_humidity_2m'
            . '&timezone=America%2FCosta_Rica';

        $datos = $this->obtenerJson($url);

        if (!$datos || empty($datos['current'])) {
            return null;
        }

        return $datos['current'];
    }

    // Consulta tipo de cambio USD a CRC con Frankfurter.
    public function obtenerTipoCambio()
    {
        $url = 'https://api.frankfurter.dev/v2/rate/USD/CRC';
        $datos = $this->obtenerJson($url);

        if (!$datos || empty($datos['rate'])) {
            return null;
        }

        return $datos;
    }

    // Ejecuta una peticion GET y decodifica JSON con tiempo limite corto.
    private function obtenerJson($url)
    {
        $contexto = stream_context_create(array(
            'http' => array(
                'method' => 'GET',
                'timeout' => 6,
                'header' => "Accept: application/json\r\n",
            ),
            'ssl' => array(
                'verify_peer' => true,
                'verify_peer_name' => true,
            ),
        ));

        $respuesta = @file_get_contents($url, false, $contexto);

        if ($respuesta === false) {
            LoggerService::registrar('error', 'api_rest', 'No se pudo consultar: ' . $url);
            return null;
        }

        $datos = json_decode($respuesta, true);

        if (!is_array($datos)) {
            LoggerService::registrar('error', 'api_rest', 'Respuesta JSON invalida: ' . $url);
            return null;
        }

        return $datos;
    }
}
