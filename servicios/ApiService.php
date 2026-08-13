<?php

// Servicio para consumir las dos APIs REST publicas del proyecto.
//
// Se usan dos servicios gratuitos que NO piden llave de acceso:
//   1. Open-Meteo   -> clima actual segun latitud y longitud del destino.
//   2. Frankfurter  -> tipo de cambio del dolar para mostrar precios en USD.
//
// Ambas se consultan desde el detalle del destino (lado del cliente) y
// tambien desde el panel de reportes del administrador.
//
// Importante: si una API no responde el metodo devuelve null y la pagina
// sigue funcionando normal, solo que sin ese dato extra.
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

    // Ejecuta una peticion GET y convierte la respuesta JSON en arreglo PHP.
    // El timeout corto (6 segundos) evita que la pagina se quede pegada
    // esperando si el servicio externo esta caido o va muy lento.
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
