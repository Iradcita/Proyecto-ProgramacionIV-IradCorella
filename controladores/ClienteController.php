<?php

// Controlador para las pantallas del cliente: catalogo, busquedas y reservas.
class ClienteController
{
    // Muestra destinos activos con filtros por texto y provincia.
    public function destinos()
    {
        exigirCliente();

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
        exigirCliente();

        $idDestino = obtenerEntero($_GET['id'] ?? 0);
        $destino = Destino::obtenerPublicoPorId($idDestino);

        if (!$destino) {
            guardarMensaje('error', 'El destino solicitado no esta disponible.');
            $this->redirigir('/destinos.php');
        }

        $hoteles = Hotel::buscarPublicos('', $idDestino, 0);
        $actividades = Actividad::buscarPublicas('', $idDestino);
        $resenas = Resena::listarAprobadasPorDestino($idDestino);
        $resenaUsuario = Resena::obtenerDeUsuarioDestino(obtenerEntero($_SESSION['usuario']['id_usuario'] ?? 0), $idDestino);
        $mensajes = obtenerMensajes();
        $csrfToken = generarTokenCsrf();
        $tituloPagina = 'Detalle de destino - NubeTurismo';

        require BASE_PATH . '/vistas/cliente/destinos/detalle.php';
    }

    // Busca hoteles activos por texto, destino y categoria.
    public function hoteles()
    {
        exigirCliente();

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
        exigirCliente();

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
        exigirCliente();

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
        exigirCliente();

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

            $actividadesIds = array_values(array_unique($actividadesIds));
            $idReservacion = Reservacion::crearCompleta($idUsuario, $fechaInicio, $fechaFin, $cantidadPersonas, 'pendiente', $observaciones, $idHotel, $cantidadHabitaciones, $actividadesIds);
            registrarBitacora('CREAR_RESERVACION_CLIENTE', 'reservaciones', $idReservacion);
            guardarMensaje('exito', 'Tu reservacion fue creada y queda pendiente de confirmacion.');
            $this->redirigir('/mis_reservaciones.php');
        } catch (Exception $e) {
            registrarExcepcion('cliente_reservacion', $e);
            guardarMensaje('error', $e->getMessage());
            $this->redirigir('/reservar.php');
        }
    }

    // Muestra el historial de reservas del cliente conectado.
    public function misReservaciones()
    {
        exigirCliente();

        $idUsuario = obtenerEntero($_SESSION['usuario']['id_usuario'] ?? 0);
        $reservaciones = Reservacion::listarPorUsuario($idUsuario);
        $mensajes = obtenerMensajes();
        $tituloPagina = 'Mis reservaciones - NubeTurismo';

        require BASE_PATH . '/vistas/cliente/mis_reservaciones.php';
    }

    // Muestra el detalle de una reservacion propia del cliente.
    public function detalleReservacion()
    {
        exigirCliente();

        $idUsuario = obtenerEntero($_SESSION['usuario']['id_usuario'] ?? 0);
        $idReservacion = obtenerEntero($_GET['id'] ?? 0);
        $reservacion = Reservacion::obtenerDetallePorUsuario($idReservacion, $idUsuario);

        if (!$reservacion) {
            guardarMensaje('error', 'La reservacion solicitada no existe.');
            $this->redirigir('/mis_reservaciones.php');
        }

        $actividades = Reservacion::listarActividadesDetallePorUsuario($idReservacion, $idUsuario);
        $mensajes = obtenerMensajes();
        $tituloPagina = 'Detalle de reservacion - NubeTurismo';

        require BASE_PATH . '/vistas/cliente/reservacion_detalle.php';
    }

    // Guarda una resena de destino enviada por el cliente.
    public function guardarResena()
    {
        exigirCliente();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/destinos.php');
        }

        if (!validarTokenCsrf($_POST['csrf_token'] ?? '')) {
            guardarMensaje('error', 'El formulario expiro. Intenta nuevamente.');
            $this->redirigir('/destinos.php');
        }

        $idUsuario = obtenerEntero($_SESSION['usuario']['id_usuario'] ?? 0);
        $idDestino = obtenerEntero($_POST['id_destino'] ?? 0);
        $calificacion = obtenerEntero($_POST['calificacion'] ?? 0);
        $comentario = trim($_POST['comentario'] ?? '');

        try {
            if (!Destino::obtenerPublicoPorId($idDestino)) {
                throw new Exception('El destino no esta disponible.');
            }

            if ($calificacion < 1 || $calificacion > 5) {
                throw new Exception('La calificacion debe estar entre 1 y 5.');
            }

            if (strlen($comentario) > 1000) {
                throw new Exception('El comentario supera el largo permitido.');
            }

            $idResena = Resena::guardar($idUsuario, $idDestino, $calificacion, $comentario);
            registrarBitacora('GUARDAR_RESENA', 'resenas_destinos', $idResena);
            guardarMensaje('exito', 'Tu resena fue enviada y queda pendiente de revision.');
        } catch (Exception $e) {
            registrarExcepcion('cliente_resena', $e);
            guardarMensaje('error', $e->getMessage());
        }

        $this->redirigir('/destinos.php?accion=detalle&id=' . $idDestino);
    }

    // Muestra el perfil editable del usuario conectado.
    public function mostrarPerfil()
    {
        exigirAutenticacion();

        $idUsuario = obtenerEntero($_SESSION['usuario']['id_usuario'] ?? 0);
        $usuario = Usuario::obtenerPorId($idUsuario);
        $mensajes = obtenerMensajes();
        $csrfToken = generarTokenCsrf();
        $tituloPagina = 'Mi perfil - NubeTurismo';

        require BASE_PATH . '/vistas/cliente/perfil.php';
    }

    // Actualiza los datos personales del usuario conectado.
    public function actualizarPerfil()
    {
        exigirAutenticacion();
        $this->validarPostPerfil();

        $idUsuario = obtenerEntero($_SESSION['usuario']['id_usuario'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');

        try {
            if ($nombre === '' || $apellidos === '') {
                throw new Exception('Nombre y apellidos son obligatorios.');
            }

            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('El correo electronico no es valido.');
            }

            $usuarioExistente = Usuario::buscarPorCorreo($correo);
            if ($usuarioExistente && (int) $usuarioExistente['id_usuario'] !== $idUsuario) {
                throw new Exception('Ese correo ya esta registrado.');
            }

            Usuario::actualizarPerfil($idUsuario, $nombre, $apellidos, $correo, $telefono);
            $this->actualizarSesionPerfil($idUsuario);
            registrarBitacora('ACTUALIZAR_PERFIL', 'usuarios', $idUsuario);
            guardarMensaje('exito', 'Perfil actualizado correctamente.');
        } catch (Exception $e) {
            registrarExcepcion('cliente_perfil', $e);
            guardarMensaje('error', $e->getMessage());
        }

        $this->redirigir('/perfil.php');
    }

    // Cambia la contrasena del usuario validando primero la actual.
    public function cambiarPassword()
    {
        exigirAutenticacion();
        $this->validarPostPerfil();

        $idUsuario = obtenerEntero($_SESSION['usuario']['id_usuario'] ?? 0);
        $passwordActual = $_POST['password_actual'] ?? '';
        $passwordNueva = $_POST['password_nueva'] ?? '';
        $confirmacion = $_POST['password_confirmacion'] ?? '';

        try {
            $usuario = Usuario::obtenerPorId($idUsuario);

            if (!$usuario || !password_verify($passwordActual, $usuario['password_hash'])) {
                throw new Exception('La contrasena actual no es correcta.');
            }

            if ($passwordNueva !== $confirmacion) {
                throw new Exception('Las contrasenas no coinciden.');
            }

            $this->validarPassword($passwordNueva);
            Usuario::actualizarPassword($idUsuario, password_hash($passwordNueva, PASSWORD_BCRYPT));
            registrarBitacora('CAMBIAR_PASSWORD', 'usuarios', $idUsuario);
            guardarMensaje('exito', 'Contrasena actualizada correctamente.');
        } catch (Exception $e) {
            registrarExcepcion('cliente_password', $e);
            guardarMensaje('error', $e->getMessage());
        }

        $this->redirigir('/perfil.php');
    }

    // Sube una nueva fotografia o elimina la fotografia actual.
    public function guardarFoto()
    {
        exigirAutenticacion();
        $this->validarPostPerfil();

        $idUsuario = obtenerEntero($_SESSION['usuario']['id_usuario'] ?? 0);

        try {
            if (!empty($_POST['eliminar_foto'])) {
                $this->eliminarArchivoFotoActual($idUsuario);
                Usuario::actualizarFoto($idUsuario, null);
                $this->actualizarSesionPerfil($idUsuario);
                registrarBitacora('ELIMINAR_FOTO_PERFIL', 'usuarios', $idUsuario);
                guardarMensaje('exito', 'Fotografia eliminada correctamente.');
                $this->redirigir('/perfil.php');
            }

            if (empty($_FILES['foto']['name'])) {
                throw new Exception('Debes seleccionar una fotografia.');
            }

            if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('No se pudo subir la fotografia.');
            }

            if ($_FILES['foto']['size'] > 2097152) {
                throw new Exception('La fotografia no debe superar 2 MB.');
            }

            $mime = $this->obtenerMimeArchivo($_FILES['foto']['tmp_name']);
            $extensiones = array('image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp');

            if (!isset($extensiones[$mime])) {
                throw new Exception('Solo se permiten imagenes JPG, PNG o WEBP.');
            }

            $directorio = BASE_PATH . '/public/uploads/perfiles';
            if (!is_dir($directorio)) {
                mkdir($directorio, 0775, true);
            }

            $nombreArchivo = 'usuario_' . $idUsuario . '_' . time() . '.' . $extensiones[$mime];
            $rutaDestino = $directorio . '/' . $nombreArchivo;

            if (!move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino)) {
                throw new Exception('No se pudo guardar la fotografia.');
            }

            $this->eliminarArchivoFotoActual($idUsuario);
            Usuario::actualizarFoto($idUsuario, 'uploads/perfiles/' . $nombreArchivo);
            $this->actualizarSesionPerfil($idUsuario);
            registrarBitacora('ACTUALIZAR_FOTO_PERFIL', 'usuarios', $idUsuario);
            guardarMensaje('exito', 'Fotografia actualizada correctamente.');
        } catch (Exception $e) {
            registrarExcepcion('cliente_foto', $e);
            guardarMensaje('error', $e->getMessage());
        }

        $this->redirigir('/perfil.php');
    }

    // Verifica fechas recibidas en formato YYYY-MM-DD.
    private function fechaEsValida($fecha)
    {
        $partes = explode('-', $fecha);

        return count($partes) === 3 && checkdate((int) $partes[1], (int) $partes[2], (int) $partes[0]);
    }

    // Valida la fortaleza minima de la nueva contrasena.
    private function validarPassword($password)
    {
        if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            throw new Exception('La contrasena debe tener al menos 8 caracteres, con letras y numeros.');
        }
    }

    // Valida metodo POST y token CSRF para formularios del perfil.
    private function validarPostPerfil()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/perfil.php');
        }

        if (!validarTokenCsrf($_POST['csrf_token'] ?? '')) {
            guardarMensaje('error', 'El formulario expiro. Intenta nuevamente.');
            $this->redirigir('/perfil.php');
        }
    }

    // Refresca en sesion los datos que aparecen en la navegacion.
    private function actualizarSesionPerfil($idUsuario)
    {
        $usuario = Usuario::obtenerPorId($idUsuario);

        if (!$usuario) {
            return;
        }

        $_SESSION['usuario']['nombre'] = $usuario['nombre'];
        $_SESSION['usuario']['apellidos'] = $usuario['apellidos'];
        $_SESSION['usuario']['correo'] = $usuario['correo'];
        $_SESSION['usuario']['telefono'] = $usuario['telefono'];
        $_SESSION['usuario']['foto_url'] = $usuario['foto_url'];
        $_SESSION['usuario']['rol_nombre'] = $usuario['rol_nombre'];
    }

    // Detecta el tipo real del archivo subido.
    private function obtenerMimeArchivo($rutaTemporal)
    {
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $rutaTemporal);
            finfo_close($finfo);

            return $mime;
        }

        if (function_exists('mime_content_type')) {
            return mime_content_type($rutaTemporal);
        }

        return '';
    }

    // Elimina del disco la foto local anterior si pertenece a la carpeta permitida.
    private function eliminarArchivoFotoActual($idUsuario)
    {
        $usuario = Usuario::obtenerPorId($idUsuario);

        if (!$usuario || empty($usuario['foto_url'])) {
            return;
        }

        if (strpos($usuario['foto_url'], 'uploads/perfiles/') !== 0) {
            return;
        }

        $ruta = BASE_PATH . '/public/' . $usuario['foto_url'];

        if (is_file($ruta)) {
            unlink($ruta);
        }
    }

    // Redirige dentro de la carpeta publica del proyecto.
    private function redirigir($ruta)
    {
        header('Location: ' . BASE_URL . $ruta);
        exit;
    }
}
