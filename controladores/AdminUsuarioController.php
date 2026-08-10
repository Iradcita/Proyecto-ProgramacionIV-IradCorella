<?php

// Controlador administrativo para gestionar usuarios y roles.
class AdminUsuarioController
{
    // Decide la accion segun el parametro recibido.
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

    // Muestra todos los usuarios con buscador.
    private function listar()
    {
        $busqueda = trim($_GET['busqueda'] ?? '');
        $usuarios = Usuario::listarTodos($busqueda);
        $mensajes = obtenerMensajes();
        $csrfToken = generarTokenCsrf();
        $tituloPagina = 'Administrar usuarios - NubeTurismo';

        require BASE_PATH . '/vistas/admin/usuarios/index.php';
    }

    // Abre formulario para crear o editar usuarios.
    private function formulario($idUsuario = 0)
    {
        $usuario = $idUsuario > 0 ? Usuario::obtenerPorId($idUsuario) : null;

        if ($idUsuario > 0 && !$usuario) {
            guardarMensaje('error', 'El usuario solicitado no existe.');
            $this->redirigir('/admin_usuarios.php');
        }

        $roles = Usuario::listarRoles();
        $mensajes = obtenerMensajes();
        $csrfToken = generarTokenCsrf();
        $tituloPagina = $usuario ? 'Editar usuario - NubeTurismo' : 'Nuevo usuario - NubeTurismo';

        require BASE_PATH . '/vistas/admin/usuarios/formulario.php';
    }

    // Valida y guarda usuarios administrados.
    private function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/admin_usuarios.php');
        }

        if (!validarTokenCsrf($_POST['csrf_token'] ?? '')) {
            guardarMensaje('error', 'El formulario expiro. Intenta nuevamente.');
            $this->redirigir('/admin_usuarios.php');
        }

        $idUsuario = obtenerEntero($_POST['id_usuario'] ?? 0);
        $idRol = obtenerEntero($_POST['id_rol'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $fotoUrl = trim($_POST['foto_url'] ?? '');
        $estado = trim($_POST['estado'] ?? 'activo');
        $password = $_POST['password'] ?? '';

        try {
            if ($idRol <= 0 || $nombre === '' || $apellidos === '') {
                throw new Exception('Rol, nombre y apellidos son obligatorios.');
            }

            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('El correo electronico no es valido.');
            }

            if (!in_array($estado, array('activo', 'inactivo', 'bloqueado'), true)) {
                throw new Exception('El estado seleccionado no es valido.');
            }

            if ($idUsuario === obtenerEntero($_SESSION['usuario']['id_usuario'] ?? 0) && ($idRol !== 1 || $estado !== 'activo')) {
                throw new Exception('No puedes quitarte tu propio acceso administrativo.');
            }

            $usuarioExistente = Usuario::buscarPorCorreo($correo);
            if ($usuarioExistente && (int) $usuarioExistente['id_usuario'] !== $idUsuario) {
                throw new Exception('Ese correo ya esta registrado.');
            }

            if ($idUsuario > 0) {
                Usuario::actualizarAdmin($idUsuario, $idRol, $nombre, $apellidos, $correo, $telefono, $fotoUrl, $estado);

                if ($password !== '') {
                    $this->validarPassword($password);
                    Usuario::actualizarPassword($idUsuario, password_hash($password, PASSWORD_BCRYPT));
                }

                $this->actualizarSesionSiCorresponde($idUsuario, $idRol, $nombre, $apellidos, $correo);
                registrarBitacora('ACTUALIZAR_USUARIO', 'usuarios', $idUsuario);
                guardarMensaje('exito', 'Usuario actualizado correctamente.');
            } else {
                $this->validarPassword($password);
                $idUsuario = Usuario::crearAdmin($idRol, $nombre, $apellidos, $correo, $telefono, $fotoUrl, password_hash($password, PASSWORD_BCRYPT), $estado);
                registrarBitacora('CREAR_USUARIO', 'usuarios', $idUsuario);
                guardarMensaje('exito', 'Usuario creado correctamente.');
            }

            $this->redirigir('/admin_usuarios.php');
        } catch (Exception $e) {
            registrarExcepcion('admin_usuarios', $e);
            guardarMensaje('error', $e->getMessage());
            $ruta = $idUsuario > 0 ? '/admin_usuarios.php?accion=editar&id=' . $idUsuario : '/admin_usuarios.php?accion=nuevo';
            $this->redirigir($ruta);
        }
    }

    // Bloquea el acceso cambiando el estado a inactivo.
    private function eliminar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/admin_usuarios.php');
        }

        if (!validarTokenCsrf($_POST['csrf_token'] ?? '')) {
            guardarMensaje('error', 'El formulario expiro. Intenta nuevamente.');
            $this->redirigir('/admin_usuarios.php');
        }

        $idUsuario = obtenerEntero($_POST['id_usuario'] ?? 0);

        if ($idUsuario === obtenerEntero($_SESSION['usuario']['id_usuario'] ?? 0)) {
            guardarMensaje('error', 'No puedes desactivar tu propio usuario.');
            $this->redirigir('/admin_usuarios.php');
        }

        Usuario::actualizarEstado($idUsuario, 'inactivo');
        registrarBitacora('DESACTIVAR_USUARIO', 'usuarios', $idUsuario);
        guardarMensaje('exito', 'Usuario desactivado correctamente.');
        $this->redirigir('/admin_usuarios.php');
    }

    // Valida la fortaleza minima de la contrasena.
    private function validarPassword($password)
    {
        if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            throw new Exception('La contrasena debe tener al menos 8 caracteres, con letras y numeros.');
        }
    }

    // Si el admin edita su propio perfil, sincroniza los datos guardados en sesion.
    private function actualizarSesionSiCorresponde($idUsuario, $idRol, $nombre, $apellidos, $correo)
    {
        if ($idUsuario !== obtenerEntero($_SESSION['usuario']['id_usuario'] ?? 0)) {
            return;
        }

        $_SESSION['usuario']['id_rol'] = $idRol;
        $_SESSION['usuario']['nombre'] = $nombre;
        $_SESSION['usuario']['apellidos'] = $apellidos;
        $_SESSION['usuario']['correo'] = $correo;
        $_SESSION['usuario']['rol_nombre'] = $idRol === 1 ? 'Administrador' : 'Cliente';
    }

    // Redirige a una ruta publica del sistema.
    private function redirigir($ruta)
    {
        header('Location: ' . BASE_URL . $ruta);
        exit;
    }
}
