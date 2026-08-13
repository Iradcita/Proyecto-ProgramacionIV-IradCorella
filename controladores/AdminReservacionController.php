<?php

// Controlador administrativo para consultar, crear, editar y cancelar reservaciones.
class AdminReservacionController
{
    // Resuelve la accion solicitada por la URL.
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

    // Presenta la lista general de reservaciones.
    private function listar()
    {
        $busqueda = trim($_GET['busqueda'] ?? '');
        $estado = trim($_GET['estado'] ?? '');
        $reservaciones = Reservacion::listarTodas($busqueda, $estado);
        $mensajes = obtenerMensajes();
        $csrfToken = generarTokenCsrf();
        $tituloPagina = 'Administrar reservaciones - NubeTurismo';

        require BASE_PATH . '/vistas/admin/reservaciones/index.php';
    }

    // Muestra el formulario de reservacion con datos relacionados.
    private function formulario($idReservacion = 0)
    {
        $reservacion = $idReservacion > 0 ? Reservacion::obtenerPorId($idReservacion) : null;

        if ($idReservacion > 0 && !$reservacion) {
            guardarMensaje('error', 'La reservacion solicitada no existe.');
            $this->redirigir('/admin_reservaciones.php');
        }

        $reservacionHotel = $idReservacion > 0 ? Reservacion::obtenerHotel($idReservacion) : null;
        $reservacionActividades = $idReservacion > 0 ? Reservacion::obtenerActividades($idReservacion) : array();
        // Se arma una lista simple con los ID de las actividades ya guardadas,
        // para poder marcar los checkbox correspondientes en el formulario.
        $actividadesSeleccionadas = array();
        foreach ($reservacionActividades as $fila) {
            $actividadesSeleccionadas[] = (int) $fila['id_actividad'];
        }

        $clientes = Usuario::listarClientesActivos();
        $hoteles = Hotel::listarActivos();
        $actividades = Actividad::listarActivas();
        $mensajes = obtenerMensajes();
        $csrfToken = generarTokenCsrf();
        $tituloPagina = $reservacion ? 'Editar reservacion - NubeTurismo' : 'Nueva reservacion - NubeTurismo';

        require BASE_PATH . '/vistas/admin/reservaciones/formulario.php';
    }

    // Valida fechas, cliente, hotel y actividades antes de guardar.
    private function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/admin_reservaciones.php');
        }

        if (!validarTokenCsrf($_POST['csrf_token'] ?? '')) {
            guardarMensaje('error', 'El formulario expiro. Intenta nuevamente.');
            $this->redirigir('/admin_reservaciones.php');
        }

        $idReservacion = obtenerEntero($_POST['id_reservacion'] ?? 0);
        $idUsuario = obtenerEntero($_POST['id_usuario'] ?? 0);
        $idHotel = obtenerEntero($_POST['id_hotel'] ?? 0);
        $fechaInicio = trim($_POST['fecha_inicio'] ?? '');
        $fechaFin = trim($_POST['fecha_fin'] ?? '');
        $cantidadPersonas = obtenerEntero($_POST['cantidad_personas'] ?? 0);
        $cantidadHabitaciones = obtenerEntero($_POST['cantidad_habitaciones'] ?? 0);
        $estado = trim($_POST['estado'] ?? 'pendiente');
        $observaciones = trim($_POST['observaciones'] ?? '');
        // Las actividades llegan como un arreglo de checkbox marcados.
        // Se recorren una por una y se convierten a numero entero por seguridad.
        $actividadesIds = array();
        if (isset($_POST['actividades']) && is_array($_POST['actividades'])) {
            foreach ($_POST['actividades'] as $idActividad) {
                $actividadesIds[] = (int) $idActividad;
            }
        }

        try {
            if ($idUsuario <= 0 || $idHotel <= 0) {
                throw new Exception('Debes seleccionar cliente y hotel.');
            }

            if (!$this->fechaEsValida($fechaInicio) || !$this->fechaEsValida($fechaFin)) {
                throw new Exception('Las fechas no tienen un formato valido.');
            }

            if (strtotime($fechaFin) <= strtotime($fechaInicio)) {
                throw new Exception('La fecha final debe ser posterior a la fecha inicial.');
            }

            if ($cantidadPersonas <= 0 || $cantidadHabitaciones <= 0) {
                throw new Exception('Personas y habitaciones deben ser valores positivos.');
            }

            if (!in_array($estado, array('pendiente', 'confirmada', 'cancelada', 'completada'), true)) {
                throw new Exception('El estado de la reservacion no es valido.');
            }

            if ($idReservacion > 0) {
                Reservacion::actualizarCompleta($idReservacion, $idUsuario, $fechaInicio, $fechaFin, $cantidadPersonas, $estado, $observaciones, $idHotel, $cantidadHabitaciones, $actividadesIds);
                registrarBitacora('ACTUALIZAR_RESERVACION', 'reservaciones', $idReservacion);
                guardarMensaje('exito', 'Reservacion actualizada correctamente.');
            } else {
                $idReservacion = Reservacion::crearCompleta($idUsuario, $fechaInicio, $fechaFin, $cantidadPersonas, $estado, $observaciones, $idHotel, $cantidadHabitaciones, $actividadesIds);
                registrarBitacora('CREAR_RESERVACION_ADMIN', 'reservaciones', $idReservacion);
                guardarMensaje('exito', 'Reservacion creada correctamente.');
            }

            $this->redirigir('/admin_reservaciones.php');
        } catch (Exception $e) {
            registrarExcepcion('admin_reservaciones', $e);
            guardarMensaje('error', $e->getMessage());
            $ruta = $idReservacion > 0 ? '/admin_reservaciones.php?accion=editar&id=' . $idReservacion : '/admin_reservaciones.php?accion=nuevo';
            $this->redirigir($ruta);
        }
    }

    // Cancela una reservacion sin eliminar su historial.
    private function eliminar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/admin_reservaciones.php');
        }

        if (!validarTokenCsrf($_POST['csrf_token'] ?? '')) {
            guardarMensaje('error', 'El formulario expiro. Intenta nuevamente.');
            $this->redirigir('/admin_reservaciones.php');
        }

        Reservacion::cancelar(obtenerEntero($_POST['id_reservacion'] ?? 0));
        registrarBitacora('CANCELAR_RESERVACION', 'reservaciones', obtenerEntero($_POST['id_reservacion'] ?? 0));
        guardarMensaje('exito', 'Reservacion cancelada correctamente.');
        $this->redirigir('/admin_reservaciones.php');
    }

    // Valida fechas en formato YYYY-MM-DD.
    private function fechaEsValida($fecha)
    {
        $partes = explode('-', $fecha);

        return count($partes) === 3 && checkdate((int) $partes[1], (int) $partes[2], (int) $partes[0]);
    }

    // Redirige usando BASE_URL.
    private function redirigir($ruta)
    {
        header('Location: ' . BASE_URL . $ruta);
        exit;
    }
}
