<?php

// Controlador administrativo para moderar resenas de destinos.
class AdminResenaController
{
    // Muestra resenas con filtro por estado.
    public function listar()
    {
        exigirAdministrador();

        $estado = trim($_GET['estado'] ?? '');
        $resenas = Resena::listarAdmin($estado);
        $mensajes = obtenerMensajes();
        $csrfToken = generarTokenCsrf();
        $tituloPagina = 'Resenas - NubeTurismo';

        require BASE_PATH . '/vistas/admin/resenas/index.php';
    }

    // Cambia estado de una resena desde moderacion.
    public function cambiarEstado()
    {
        exigirAdministrador();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/admin_resenas.php');
        }

        if (!validarTokenCsrf($_POST['csrf_token'] ?? '')) {
            guardarMensaje('error', 'El formulario expiro. Intenta nuevamente.');
            $this->redirigir('/admin_resenas.php');
        }

        $idResena = obtenerEntero($_POST['id_resena'] ?? 0);
        $estado = trim($_POST['estado'] ?? '');

        if (!in_array($estado, array('pendiente', 'aprobada', 'rechazada'), true)) {
            guardarMensaje('error', 'El estado seleccionado no es valido.');
            $this->redirigir('/admin_resenas.php');
        }

        try {
            Resena::cambiarEstado($idResena, $estado);
            registrarBitacora('MODERAR_RESENA', 'resenas_destinos', $idResena);
            guardarMensaje('exito', 'Resena actualizada correctamente.');
        } catch (Exception $e) {
            registrarExcepcion('admin_resenas', $e);
            guardarMensaje('error', 'No fue posible actualizar la resena.');
        }

        $this->redirigir('/admin_resenas.php');
    }

    // Redirige al modulo de resenas.
    private function redirigir($ruta)
    {
        header('Location: ' . BASE_URL . $ruta);
        exit;
    }
}
