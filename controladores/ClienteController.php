<?php

// Controlador para las pantallas del cliente: catalogo, busquedas y reservas.
class ClienteController
{
    // Muestra destinos activos con filtros por texto y provincia.
    public function destinos()
    {
        exigirAutenticacion();

        $busqueda = trim($_GET['busqueda'] ?? '');
        $idProvincia = obtenerEntero($_GET['provincia'] ?? 0);
        $destinos = Destino::listarPublicos($busqueda, $idProvincia);
        $provincias = Destino::listarProvincias();
        $mensajes = obtenerMensajes();
        $tituloPagina = 'Destinos - NubeTurismo';

        require BASE_PATH . '/vistas/cliente/destinos/index.php';
    }

    // Presenta el detalle de un destino con hoteles y actividades relacionadas.
    public function detalleDestino()
    {
        exigirAutenticacion();

        $idDestino = obtenerEntero($_GET['id'] ?? 0);
        $destino = Destino::obtenerPublicoPorId($idDestino);

        if (!$destino) {
            guardarMensaje('error', 'El destino solicitado no esta disponible.');
            $this->redirigir('/destinos.php');
        }

        $hoteles = Hotel::buscarPublicos('', $idDestino, 0);
        $actividades = Actividad::buscarPublicas('', $idDestino);
        $mensajes = obtenerMensajes();
        $tituloPagina = 'Detalle de destino - NubeTurismo';

        require BASE_PATH . '/vistas/cliente/destinos/detalle.php';
    }

    // Busca hoteles activos por texto, destino y categoria.
    public function hoteles()
    {
        exigirAutenticacion();

        $busqueda = trim($_GET['busqueda'] ?? '');
        $idDestino = obtenerEntero($_GET['destino'] ?? 0);
        $categoria = obtenerEntero($_GET['categoria'] ?? 0);
        $hoteles = Hotel::buscarPublicos($busqueda, $idDestino, $categoria);
        $destinos = Destino::listarActivos();
        $mensajes = obtenerMensajes();
        $tituloPagina = 'Hoteles - NubeTurismo';

        require BASE_PATH . '/vistas/cliente/hoteles.php';
    }

    // Busca actividades activas por texto y destino.
    public function actividades()
    {
        exigirAutenticacion();

        $busqueda = trim($_GET['busqueda'] ?? '');
        $idDestino = obtenerEntero($_GET['destino'] ?? 0);
        $actividades = Actividad::buscarPublicas($busqueda, $idDestino);
        $destinos = Destino::listarActivos();
        $mensajes = obtenerMensajes();
        $tituloPagina = 'Actividades - NubeTurismo';

        require BASE_PATH . '/vistas/cliente/actividades.php';
    }

    // Abre el formulario de reserva para el cliente conectado.
    public function mostrarReservar()
    {
        exigirAutenticacion();

        $idDestino = obtenerEntero($_GET['destino'] ?? 0);
        $hotelPreseleccionado = obtenerEntero($_GET['hotel'] ?? 0);
        $hoteles = Hotel::buscarPublicos('', $idDestino, 0);
        $actividades = Actividad::buscarPublicas('', $idDestino);
        $destinos = Destino::listarActivos();
        $mensajes = obtenerMensajes();
        $csrfToken = generarTokenCsrf();
        $tituloPagina = 'Reservar - NubeTurismo';

        require BASE_PATH . '/vistas/cliente/reservar.php';
    }

    // Guarda la reserva nueva del cliente autenticado.
    public function guardarReservacion()
    {
        exigirAutenticacion();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/reservar.php');
        }

        if (!validarTokenCsrf($_POST['csrf_token'] ?? '')) {
            guardarMensaje('error', 'El formulario expiro. Intenta nuevamente.');
            $this->redirigir('/reservar.php');
        }

        $idUsuario = obtenerEntero($_SESSION['usuario']['id_usuario'] ?? 0);
        $idHotel = obtenerEntero($_POST['id_hotel'] ?? 0);
        $fechaInicio = trim($_POST['fecha_inicio'] ?? '');
        $fechaFin = trim($_POST['fecha_fin'] ?? '');
        $cantidadPersonas = obtenerEntero($_POST['cantidad_personas'] ?? 0);
        $cantidadHabitaciones = obtenerEntero($_POST['cantidad_habitaciones'] ?? 0);
        $observaciones = trim($_POST['observaciones'] ?? '');
        $actividadesIds = isset($_POST['actividades']) && is_array($_POST['actividades'])
            ? array_map('intval', $_POST['actividades'])
            : array();

        try {
            if (!Hotel::obtenerActivoPorId($idHotel)) {
                throw new Exception('Debes seleccionar un hotel disponible.');
            }

            foreach ($actividadesIds as $idActividad) {
                if (!Actividad::obtenerActivaPorId($idActividad)) {
                    throw new Exception('Una actividad seleccionada ya no esta disponible.');
                }
            }

            if (!$this->fechaEsValida($fechaInicio) || !$this->fechaEsValida($fechaFin)) {
                throw new Exception('Las fechas no tienen un formato valido.');
            }

            if (strtotime($fechaInicio) < strtotime(date('Y-m-d'))) {
                throw new Exception('La fecha inicial no puede estar en el pasado.');
            }

            if (strtotime($fechaFin) <= strtotime($fechaInicio)) {
                throw new Exception('La fecha final debe ser posterior a la fecha inicial.');
            }

            if ($cantidadPersonas <= 0 || $cantidadHabitaciones <= 0) {
                throw new Exception('Personas y habitaciones deben ser valores positivos.');
            }

            Reservacion::crearCompleta($idUsuario, $fechaInicio, $fechaFin, $cantidadPersonas, 'pendiente', $observaciones, $idHotel, $cantidadHabitaciones, $actividadesIds);
            guardarMensaje('exito', 'Tu reservacion fue creada y queda pendiente de confirmacion.');
            $this->redirigir('/mis_reservaciones.php');
        } catch (Exception $e) {
            guardarMensaje('error', $e->getMessage());
            $this->redirigir('/reservar.php');
        }
    }

    // Muestra el historial de reservas del cliente conectado.
    public function misReservaciones()
    {
        exigirAutenticacion();

        $idUsuario = obtenerEntero($_SESSION['usuario']['id_usuario'] ?? 0);
        $reservaciones = Reservacion::listarPorUsuario($idUsuario);
        $mensajes = obtenerMensajes();
        $tituloPagina = 'Mis reservaciones - NubeTurismo';

        require BASE_PATH . '/vistas/cliente/mis_reservaciones.php';
    }

    // Verifica fechas recibidas en formato YYYY-MM-DD.
    private function fechaEsValida($fecha)
    {
        $partes = explode('-', $fecha);

        return count($partes) === 3 && checkdate((int) $partes[1], (int) $partes[2], (int) $partes[0]);
    }

    // Redirige dentro de la carpeta publica del proyecto.
    private function redirigir($ruta)
    {
        header('Location: ' . BASE_URL . $ruta);
        exit;
    }
}
