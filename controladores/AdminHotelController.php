<?php

// Controlador administrativo para crear, editar, listar y desactivar hoteles.
class AdminHotelController
{
    // Selecciona la accion solicitada desde la pagina publica.
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

    // Presenta hoteles con busqueda y filtro por destino.
    private function listar()
    {
        $busqueda = trim($_GET['busqueda'] ?? '');
        $idDestino = obtenerEntero($_GET['destino'] ?? 0);
        $hoteles = Hotel::listar($busqueda, $idDestino);
        $destinos = Destino::listarActivos();
        $mensajes = obtenerMensajes();
        $csrfToken = generarTokenCsrf();
        $tituloPagina = 'Administrar hoteles - NubeTurismo';

        require BASE_PATH . '/vistas/admin/hoteles/index.php';
    }

    // Muestra formulario de creacion o edicion.
    private function formulario($idHotel = 0)
    {
        $hotel = $idHotel > 0 ? Hotel::obtenerPorId($idHotel) : null;

        if ($idHotel > 0 && !$hotel) {
            guardarMensaje('error', 'El hotel solicitado no existe.');
            $this->redirigir('/admin_hoteles.php');
        }

        $destinos = Destino::listarActivos();
        $mensajes = obtenerMensajes();
        $csrfToken = generarTokenCsrf();
        $tituloPagina = $hotel ? 'Editar hotel - NubeTurismo' : 'Nuevo hotel - NubeTurismo';

        require BASE_PATH . '/vistas/admin/hoteles/formulario.php';
    }

    // Valida datos del hotel y los guarda en la base.
    private function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/admin_hoteles.php');
        }

        if (!validarTokenCsrf($_POST['csrf_token'] ?? '')) {
            guardarMensaje('error', 'El formulario expiro. Intenta nuevamente.');
            $this->redirigir('/admin_hoteles.php');
        }

        $idHotel = obtenerEntero($_POST['id_hotel'] ?? 0);
        $idDestino = obtenerEntero($_POST['id_destino'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $categoria = obtenerEntero($_POST['categoria'] ?? 0);
        $direccion = trim($_POST['direccion'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $precioNoche = trim($_POST['precio_noche'] ?? '');
        $cantidadHabitaciones = obtenerEntero($_POST['cantidad_habitaciones'] ?? 0);
        $descripcion = trim($_POST['descripcion'] ?? '');
        // La imagen ya no se escribe a mano: se sube como archivo.
        // Aqui se guarda la que el registro ya tenia, y mas abajo se cambia
        // solo si el administrador subio una nueva o pidio quitarla.
        $imagen = trim($_POST['imagen_actual'] ?? '');
        $estado = obtenerEntero($_POST['estado'] ?? 1);

        try {
            if ($idDestino <= 0 || $nombre === '' || $direccion === '' || $descripcion === '') {
                throw new Exception('Destino, nombre, direccion y descripcion son obligatorios.');
            }

            if ($categoria < 1 || $categoria > 5) {
                throw new Exception('La categoria debe estar entre 1 y 5.');
            }

            if (!is_numeric($precioNoche) || (float) $precioNoche <= 0) {
                throw new Exception('El precio por noche debe ser un numero positivo.');
            }

            if ($cantidadHabitaciones <= 0) {
                throw new Exception('La cantidad de habitaciones debe ser positiva.');
            }

            if ($telefono !== '' && !telefonoEsValido($telefono)) {
                throw new Exception('El telefono del hotel debe tener 8 digitos. Ejemplo: 2222-3333.');
            }

            if ($correo !== '' && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('El correo del hotel no es valido.');
            }


            // Caso 1: marco la casilla para quitar la imagen actual.
            if (!empty($_POST['quitar_imagen'])) {
                ImagenService::eliminar($imagen);
                $imagen = '';
            }

            // Caso 2: escogio un archivo nuevo. Se sube y se borra el anterior.
            if (ImagenService::seSubioArchivo($_FILES['imagen_archivo'] ?? null)) {
                $imagenAnterior = $imagen;
                $imagen = ImagenService::guardar($_FILES['imagen_archivo'], 'hoteles', 'hotel');
                ImagenService::eliminar($imagenAnterior);
            }

            if ($idHotel > 0) {
                Hotel::actualizar($idHotel, $idDestino, $nombre, $categoria, $direccion, $telefono, $correo, $precioNoche, $cantidadHabitaciones, $descripcion, $imagen, $estado);
                registrarBitacora('ACTUALIZAR_HOTEL', 'hoteles', $idHotel);
                guardarMensaje('exito', 'Hotel actualizado correctamente.');
            } else {
                $idHotel = Hotel::crear($idDestino, $nombre, $categoria, $direccion, $telefono, $correo, $precioNoche, $cantidadHabitaciones, $descripcion, $imagen, $estado);
                registrarBitacora('CREAR_HOTEL', 'hoteles', $idHotel);
                guardarMensaje('exito', 'Hotel creado correctamente.');
            }

            $this->redirigir('/admin_hoteles.php');
        } catch (Exception $e) {
            registrarExcepcion('admin_hoteles', $e);
            guardarMensaje('error', $e->getMessage());
            $ruta = $idHotel > 0 ? '/admin_hoteles.php?accion=editar&id=' . $idHotel : '/admin_hoteles.php?accion=nuevo';
            $this->redirigir($ruta);
        }
    }

    // Desactiva el hotel para conservar reservas historicas.
    private function eliminar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/admin_hoteles.php');
        }

        if (!validarTokenCsrf($_POST['csrf_token'] ?? '')) {
            guardarMensaje('error', 'El formulario expiro. Intenta nuevamente.');
            $this->redirigir('/admin_hoteles.php');
        }

        Hotel::desactivar(obtenerEntero($_POST['id_hotel'] ?? 0));
        registrarBitacora('DESACTIVAR_HOTEL', 'hoteles', obtenerEntero($_POST['id_hotel'] ?? 0));
        guardarMensaje('exito', 'Hotel desactivado correctamente.');
        $this->redirigir('/admin_hoteles.php');
    }

    // Redireccion centralizada para este controlador.
    private function redirigir($ruta)
    {
        header('Location: ' . BASE_URL . $ruta);
        exit;
    }
}
