<?php

// Controlador de autenticacion: login, logout, registro y recuperacion de contrasena.
class AuthController
{
    private $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function mostrarLogin()
    {
        if (usuarioAutenticado()) {
            $this->redirigir('/index.php');
        }

        $csrfToken = generarTokenCsrf();
        $mensajes = obtenerMensajes();

        require BASE_PATH . '/vistas/auth/login.php';
    }

    public function procesarLogin()
    {
        //si un usuario manda algo que no sea post, lo redirigimos al login
        //evitamos que un usuario pueda acceder a este metodo desde la url
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/login.php');
        }

        if (!validarTokenCsrf(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
            guardarMensaje('error', 'Tu sesión de formulario expiró, intenta de nuevo.');
            $this->redirigir('/login.php');
        }

        $correo = trim(isset($_POST['correo']) ? $_POST['correo'] : '');
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        if ($correo === '' || $password === '') {
            guardarMensaje('error', 'Debes indicar el correo y la contraseña.');
            $this->redirigir('/login.php');
        }

        try {
            $usuario = $this->authService->login($correo, $password);

            session_regenerate_id(true);
            $_SESSION['usuario'] = array(
                'id_usuario' => $usuario['id_usuario'],
                'nombre' => $usuario['nombre'],
                'apellidos' => $usuario['apellidos'],
                'correo' => $usuario['correo'],
                'id_rol' => $usuario['id_rol'],
                'rol_nombre' => $usuario['rol_nombre'],
            );

            $this->redirigir('/index.php');
        } catch (Exception $e) {
            guardarMensaje('error', $e->getMessage());
            $this->redirigir('/login.php');
        }
    }

    public function procesarLogout()
    {
        cerrarSesion();
        $this->redirigir('/login.php');
    }

    public function mostrarRegistro()
    {
        if (usuarioAutenticado()) {
            $this->redirigir('/index.php');
        }

        $csrfToken = generarTokenCsrf();
        $mensajes = obtenerMensajes();
        $valores = isset($_SESSION['registro_valores']) ? $_SESSION['registro_valores'] : array();
        unset($_SESSION['registro_valores']);

        require BASE_PATH . '/vistas/auth/registro.php';
    }

    public function procesarRegistro()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/registro.php');
        }

        if (!validarTokenCsrf(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
            guardarMensaje('error', 'Tu sesión de formulario expiró, intenta de nuevo.');
            $this->redirigir('/registro.php');
        }

        $nombre = trim(isset($_POST['nombre']) ? $_POST['nombre'] : '');
        $apellidos = trim(isset($_POST['apellidos']) ? $_POST['apellidos'] : '');
        $correo = trim(isset($_POST['correo']) ? $_POST['correo'] : '');
        $telefono = trim(isset($_POST['telefono']) ? $_POST['telefono'] : '');
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $confirmacion = isset($_POST['password_confirmacion']) ? $_POST['password_confirmacion'] : '';

        try {
            $this->authService->registrar($nombre, $apellidos, $correo, $telefono, $password, $confirmacion);
            guardarMensaje('exito', 'Tu cuenta se creó correctamente. Ya puedes iniciar sesión.');
            $this->redirigir('/login.php');
        } catch (Exception $e) {
            $_SESSION['registro_valores'] = array(
                'nombre' => $nombre,
                'apellidos' => $apellidos,
                'correo' => $correo,
                'telefono' => $telefono,
            );
            guardarMensaje('error', $e->getMessage());
            $this->redirigir('/registro.php');
        }
    }

    public function mostrarRecuperar()
    {
        $csrfToken = generarTokenCsrf();
        $mensajes = obtenerMensajes();
        $enlaceRestablecimiento = isset($_SESSION['enlace_restablecimiento']) ? $_SESSION['enlace_restablecimiento'] : null;
        unset($_SESSION['enlace_restablecimiento']);

        require BASE_PATH . '/vistas/auth/recuperar.php';
    }

    public function procesarRecuperar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/recuperar.php');
        }

        if (!validarTokenCsrf(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
            guardarMensaje('error', 'Tu sesión de formulario expiró, intenta de nuevo.');
            $this->redirigir('/recuperar.php');
        }

        $correo = trim(isset($_POST['correo']) ? $_POST['correo'] : '');
        $token = $this->authService->solicitarRestablecimiento($correo);

        if ($token !== null) {
            $_SESSION['enlace_restablecimiento'] = BASE_URL . '/restablecer.php?token=' . urlencode($token);
        }

        guardarMensaje('exito', 'Si el correo está registrado, se generó un enlace de restablecimiento (simulado, válido 30 min).');
        $this->redirigir('/recuperar.php');
    }

    public function mostrarRestablecer()
    {
        $token = isset($_GET['token']) ? $_GET['token'] : '';

        if ($token === '') {
            guardarMensaje('error', 'El enlace de restablecimiento no es válido.');
            $this->redirigir('/recuperar.php');
        }

        $csrfToken = generarTokenCsrf();
        $mensajes = obtenerMensajes();

        require BASE_PATH . '/vistas/auth/restablecer.php';
    }

    public function procesarRestablecer()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/recuperar.php');
        }

        if (!validarTokenCsrf(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
            guardarMensaje('error', 'Tu sesión de formulario expiró, intenta de nuevo.');
            $this->redirigir('/recuperar.php');
        }

        $token = isset($_POST['token']) ? $_POST['token'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $confirmacion = isset($_POST['password_confirmacion']) ? $_POST['password_confirmacion'] : '';

        try {
            $this->authService->restablecerPassword($token, $password, $confirmacion);
            guardarMensaje('exito', 'Tu contraseña se actualizó correctamente. Ya puedes iniciar sesión.');
            $this->redirigir('/login.php');
        } catch (Exception $e) {
            guardarMensaje('error', $e->getMessage());
            $this->redirigir('/restablecer.php?token=' . urlencode($token));
        }
    }

    private function redirigir($ruta)
    {
        header('Location: ' . BASE_URL . $ruta);
        exit;
    }
}
