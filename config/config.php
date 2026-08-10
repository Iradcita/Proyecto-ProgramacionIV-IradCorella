<?php

// Datos de conexion a la base de datos (XAMPP por defecto: usuario root sin clave)
define('DB_HOST', 'localhost');
define('DB_NAME', 'nubeturismo_db');
define('DB_USER', 'root');
define('DB_PASS', '');

define('BASE_PATH', dirname(__DIR__));

// Calculamos la url base a partir de la carpeta htdocs, porque el proyecto
// no siempre queda directo en htdocs (en mi caso queda en htdocs/xampp/...)
$raizDocumentos = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']));
$raizProyecto = str_replace('\\', '/', realpath(BASE_PATH));
$rutaProyecto = rtrim(substr($raizProyecto, strlen($raizDocumentos)), '/');

define('BASE_URL', $rutaProyecto . '/public');
define('RECURSOS_URL', $rutaProyecto . '/recursos');

// Incluimos las clases del sistema
require_once BASE_PATH . '/config/Database.php';
require_once BASE_PATH . '/config/funciones.php';
require_once BASE_PATH . '/modelos/Usuario.php';
require_once BASE_PATH . '/modelos/PasswordReset.php';
require_once BASE_PATH . '/modelos/Destino.php';
require_once BASE_PATH . '/modelos/Hotel.php';
require_once BASE_PATH . '/modelos/Actividad.php';
require_once BASE_PATH . '/modelos/Reservacion.php';
require_once BASE_PATH . '/servicios/AuthService.php';
require_once BASE_PATH . '/controladores/AuthController.php';
require_once BASE_PATH . '/controladores/AdminDestinoController.php';
require_once BASE_PATH . '/controladores/AdminHotelController.php';
require_once BASE_PATH . '/controladores/AdminActividadController.php';
require_once BASE_PATH . '/controladores/AdminUsuarioController.php';
require_once BASE_PATH . '/controladores/AdminReservacionController.php';
require_once BASE_PATH . '/controladores/ClienteController.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
