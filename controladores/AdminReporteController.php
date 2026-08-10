<?php

// Controlador administrativo para reportes, graficos y datos externos.
class AdminReporteController
{
    private $apiService;

    public function __construct()
    {
        $this->apiService = new ApiService();
    }

    // Carga todos los reportes requeridos y los datos de APIs publicas.
    public function mostrar()
    {
        exigirAdministrador();

        if (!empty($_GET['exportar'])) {
            $this->exportar($_GET['exportar']);
        }

        $resumen = Reporte::resumenGeneral();
        $reservacionesPorDestino = Reporte::reservacionesPorDestino();
        $hotelesMasReservados = Reporte::hotelesMasReservados();
        $actividadesMasSolicitadas = Reporte::actividadesMasSolicitadas();
        $usuariosRegistrados = Reporte::usuariosRegistrados();
        $reservacionesPorFecha = Reporte::reservacionesPorFecha();
        $ingresosEstimados = Reporte::ingresosEstimados();
        $destinoClima = Reporte::destinoConCoordenadas();
        $clima = $this->apiService->obtenerClima($destinoClima['latitud'], $destinoClima['longitud']);
        $tipoCambio = $this->apiService->obtenerTipoCambio();
        $mensajes = obtenerMensajes();
        $tituloPagina = 'Reportes - NubeTurismo';

        require BASE_PATH . '/vistas/admin/reportes/index.php';
    }

    // Exporta reportes seleccionados en formato CSV.
    private function exportar($tipo)
    {
        $mapa = array(
            'destinos' => array('archivo' => 'reservaciones_por_destino.csv', 'datos' => Reporte::reservacionesPorDestino()),
            'hoteles' => array('archivo' => 'hoteles_mas_reservados.csv', 'datos' => Reporte::hotelesMasReservados()),
            'actividades' => array('archivo' => 'actividades_mas_solicitadas.csv', 'datos' => Reporte::actividadesMasSolicitadas()),
            'fechas' => array('archivo' => 'reservaciones_por_fecha.csv', 'datos' => Reporte::reservacionesPorFecha()),
            'ingresos' => array('archivo' => 'ingresos_estimados.csv', 'datos' => Reporte::ingresosEstimados()),
        );

        if (empty($mapa[$tipo])) {
            guardarMensaje('error', 'El reporte solicitado no existe.');
            header('Location: ' . BASE_URL . '/admin_reportes.php');
            exit;
        }

        registrarBitacora('EXPORTAR_REPORTE_' . strtoupper($tipo), 'reportes', null);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $mapa[$tipo]['archivo'] . '"');

        $salida = fopen('php://output', 'w');
        $datos = $mapa[$tipo]['datos'];

        if (!empty($datos)) {
            fputcsv($salida, array_keys($datos[0]));
            foreach ($datos as $fila) {
                fputcsv($salida, $fila);
            }
        }

        fclose($salida);
        exit;
    }
}
