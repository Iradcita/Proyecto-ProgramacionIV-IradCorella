<?php

// Controlador administrativo para el CRUD de actividades turisticas.
class AdminActividadController
{
    // Dirige la solicitud hacia listar, formulario, guardar o eliminar.
    public function manejar()
    {
        exigirAdministrador();

        $accion = isset($_GET['accion']) ? $_GET['accion'] : 'listar';

        if ($accion === 'nuevo') {
            $this->formulario();
        } elseif ($accion === 'editar') {
            $this->formulario(obtenerEntero($_GET['id'] ?? 0));
        } elseif ($accion === 'guardar') {
            $this->guardar();
        } elseif ($accion === 'eliminar') {
            $this->eliminar();
        } else {
            $this->listar();
        }
    }

    // Muestra actividades con filtros por texto y destino.
    private function listar()
    {
        $busqueda = trim($_GET['busqueda'] ?? '');
        $idDestino = obtenerEntero($_GET['destino'] ?? 0);
        $actividades = Actividad::listar($busqueda, $idDestino);
        $destinos = Destino::listarActivos();
        $mensajes = obtenerMensajes();
        $csrfToken = generarTokenCsrf();
        $tituloPagina = 'Administrar actividades - NubeTurismo';

        require BASE_PATH . '/vistas/admin/actividades/index.php';
    }

    // Carga el formulario de creacion o edicion.
    private function formulario($idActividad = 0)
    {
        $actividad = $idActividad > 0 ? Actividad::obtenerPorId($idActividad) : null;

        if ($idActividad > 0 && !$actividad) {
            guardarMensaje('error', 'La actividad solicitada no existe.');
            $this->redirigir('/admin_actividades.php');
        }

        $destinos = Destino::listarActivos();
        $mensajes = obtenerMensajes();
        $csrfToken = generarTokenCsrf();
        $tituloPagina = $actividad ? 'Editar actividad - NubeTurismo' : 'Nueva actividad - NubeTurismo';

        require BASE_PATH . '/vistas/admin/actividades/formulario.php';
    }

    // Valida y persiste la actividad.
    private function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/admin_actividades.php');
        }

        if (!validarTokenCsrf($_POST['csrf_token'] ?? '')) {
            guardarMensaje('error', 'El formulario expiro. Intenta nuevamente.');
            $this->redirigir('/admin_actividades.php');
        }

        $idActividad = obtenerEntero($_POST['id_actividad'] ?? 0);
        $idDestino = obtenerEntero($_POST['id_destino'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $precio = trim($_POST['precio'] ?? '');
        $duracionMinutos = obtenerEntero($_POST['duracion_minutos'] ?? 0);
        $cupoMaximo = obtenerEntero($_POST['cupo_maximo'] ?? 0);
        // La imagen ya no se escribe a mano: se sube como archivo.
        // Aqui se guarda la que el registro ya tenia, y mas abajo se cambia
        // solo si el administrador subio una nueva o pidio quitarla.
        $imagen = trim($_POST['imagen_actual'] ?? '');
        $estado = obtenerEntero($_POST['estado'] ?? 1);

        try {
            if ($idDestino <= 0 || $nombre === '' || $descripcion === '') {
                throw new Exception('Destino, nombre y descripcion son obligatorios.');
            }

            if (!is_numeric($precio) || (float) $precio <= 0) {
                throw new Exception('El precio debe ser un numero positivo.');
            }

            if ($duracionMinutos <= 0 || $cupoMaximo <= 0) {
                throw new Exception('La duracion y el cupo deben ser positivos.');
            }


            // Caso 1: marco la casilla para quitar la imagen actual.
            if (!empty($_POST['quitar_imagen'])) {
                ImagenService::eliminar($imagen);
                $imagen = '';
            }

            // Caso 2: escogio un archivo nuevo. Se sube y se borra el anterior.
            if (ImagenService::seSubioArchivo($_FILES['imagen_archivo'] ?? null)) {
                $imagenAnterior = $imagen;
                $imagen = ImagenService::guardar($_FILES['imagen_archivo'], 'actividades', 'actividad');
                ImagenService::eliminar($imagenAnterior);
            }

            if ($idActividad > 0) {
                Actividad::actualizar($idActividad, $idDestino, $nombre, $descripcion, $precio, $duracionMinutos, $cupoMaximo, $imagen, $estado);
                registrarBitacora('ACTUALIZAR_ACTIVIDAD', 'actividades', $idActividad);
                guardarMensaje('exito', 'Actividad actualizada correctamente.');
            } else {
                $idActividad = Actividad::crear($idDestino, $nombre, $descripcion, $precio, $duracionMinutos, $cupoMaximo, $imagen, $estado);
                registrarBitacora('CREAR_ACTIVIDAD', 'actividades', $idActividad);
                guardarMensaje('exito', 'Actividad creada correctamente.');
            }

            $this->redirigir('/admin_actividades.php');
        } catch (Exception $e) {
            registrarExcepcion('admin_actividades', $e);
            guardarMensaje('error', $e->getMessage());
            $ruta = $idActividad > 0 ? '/admin_actividades.php?accion=editar&id=' . $idActividad : '/admin_actividades.php?accion=nuevo';
            $this->redirigir($ruta);
        }
    }

    // Desactiva la actividad manteniendo el historial de reservaciones.
    private function eliminar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/admin_actividades.php');
        }

        if (!validarTokenCsrf($_POST['csrf_token'] ?? '')) {
            guardarMensaje('error', 'El formulario expiro. Intenta nuevamente.');
            $this->redirigir('/admin_actividades.php');
        }

        Actividad::desactivar(obtenerEntero($_POST['id_actividad'] ?? 0));
        registrarBitacora('DESACTIVAR_ACTIVIDAD', 'actividades', obtenerEntero($_POST['id_actividad'] ?? 0));
        guardarMensaje('exito', 'Actividad desactivada correctamente.');
        $this->redirigir('/admin_actividades.php');
    }

    // Aplica redirecciones relativas a BASE_URL.
    private function redirigir($ruta)
    {
        header('Location: ' . BASE_URL . $ruta);
        exit;
    }
}
