<?php

// Controlador administrativo para el CRUD de destinos turisticos.
class AdminDestinoController
{
    // Punto de entrada unico: decide que accion ejecutar segun la URL.
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

    // Muestra la tabla de destinos con filtros.
    private function listar()
    {
        $busqueda = trim($_GET['busqueda'] ?? '');
        $idProvincia = obtenerEntero($_GET['provincia'] ?? 0);
        $destinos = Destino::listar($busqueda, $idProvincia);
        $provincias = Destino::listarProvincias();
        $mensajes = obtenerMensajes();
        $csrfToken = generarTokenCsrf();
        $tituloPagina = 'Administrar destinos - NubeTurismo';

        require BASE_PATH . '/vistas/admin/destinos/index.php';
    }

    // Muestra formulario vacio o cargado para crear/editar.
    private function formulario($idDestino = 0)
    {
        $destino = $idDestino > 0 ? Destino::obtenerPorId($idDestino) : null;

        if ($idDestino > 0 && !$destino) {
            guardarMensaje('error', 'El destino solicitado no existe.');
            $this->redirigir('/admin_destinos.php');
        }

        $provincias = Destino::listarProvincias();
        $mensajes = obtenerMensajes();
        $csrfToken = generarTokenCsrf();
        $tituloPagina = $destino ? 'Editar destino - NubeTurismo' : 'Nuevo destino - NubeTurismo';

        require BASE_PATH . '/vistas/admin/destinos/formulario.php';
    }

    // Valida y guarda un destino enviado por POST.
    private function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/admin_destinos.php');
        }

        if (!validarTokenCsrf($_POST['csrf_token'] ?? '')) {
            guardarMensaje('error', 'El formulario expiro. Intenta nuevamente.');
            $this->redirigir('/admin_destinos.php');
        }

        $idDestino = obtenerEntero($_POST['id_destino'] ?? 0);
        $idProvincia = obtenerEntero($_POST['id_provincia'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $imagen = trim($_POST['imagen_principal'] ?? '');
        $latitud = trim($_POST['latitud'] ?? '');
        $longitud = trim($_POST['longitud'] ?? '');
        $estado = obtenerEntero($_POST['estado'] ?? 1);

        try {
            if ($idProvincia <= 0 || $nombre === '' || $descripcion === '') {
                throw new Exception('Provincia, nombre y descripcion son obligatorios.');
            }

            $latitud = $latitud === '' ? null : $latitud;
            $longitud = $longitud === '' ? null : $longitud;

            if ($idDestino > 0) {
                Destino::actualizar($idDestino, $idProvincia, $nombre, $descripcion, $imagen, $latitud, $longitud, $estado);
                registrarBitacora('ACTUALIZAR_DESTINO', 'destinos', $idDestino);
                guardarMensaje('exito', 'Destino actualizado correctamente.');
            } else {
                $idDestino = Destino::crear($idProvincia, $nombre, $descripcion, $imagen, $latitud, $longitud, $estado);
                registrarBitacora('CREAR_DESTINO', 'destinos', $idDestino);
                guardarMensaje('exito', 'Destino creado correctamente.');
            }

            $this->redirigir('/admin_destinos.php');
        } catch (Exception $e) {
            registrarExcepcion('admin_destinos', $e);
            guardarMensaje('error', $e->getMessage());
            $ruta = $idDestino > 0 ? '/admin_destinos.php?accion=editar&id=' . $idDestino : '/admin_destinos.php?accion=nuevo';
            $this->redirigir($ruta);
        }
    }

    // Desactiva un destino seleccionado desde la tabla.
    private function eliminar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/admin_destinos.php');
        }

        if (!validarTokenCsrf($_POST['csrf_token'] ?? '')) {
            guardarMensaje('error', 'El formulario expiro. Intenta nuevamente.');
            $this->redirigir('/admin_destinos.php');
        }

        Destino::desactivar(obtenerEntero($_POST['id_destino'] ?? 0));
        registrarBitacora('DESACTIVAR_DESTINO', 'destinos', obtenerEntero($_POST['id_destino'] ?? 0));
        guardarMensaje('exito', 'Destino desactivado correctamente.');
        $this->redirigir('/admin_destinos.php');
    }

    // Redirige siempre usando la URL base del proyecto.
    private function redirigir($ruta)
    {
        header('Location: ' . BASE_URL . $ruta);
        exit;
    }
}
