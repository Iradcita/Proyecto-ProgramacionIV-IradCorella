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
}
