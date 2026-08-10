<?php

// Controlador administrativo para consultar la bitacora del sistema.
class AdminBitacoraController
{
    // Muestra acciones recientes con filtro por texto.
    public function mostrar()
    {
        exigirAdministrador();

        $busqueda = trim($_GET['busqueda'] ?? '');
        $acciones = Bitacora::listar($busqueda);
        $mensajes = obtenerMensajes();
        $tituloPagina = 'Bitacora - NubeTurismo';

        require BASE_PATH . '/vistas/admin/bitacora/index.php';
    }
}
