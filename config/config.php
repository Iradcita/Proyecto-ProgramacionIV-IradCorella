<?php

/* ============================================================================
   ARCHIVO DE CONFIGURACION GENERAL
   ----------------------------------------------------------------------------
   Este es el primer archivo que carga cada pagina del sistema.
   Se encarga de cuatro cosas, en este orden:

     1. Guardar los datos de conexion a la base de datos.
     2. Calcular las rutas (URL) del proyecto.
     3. Cargar todas las clases que usa el sistema.
     4. Iniciar la sesion del usuario.

   Cada pagina de la carpeta "public" empieza con:
       require dirname(__DIR__) . '/config/config.php';
   ============================================================================ */


/* ---------------------------------------------------------------------------
   1. DATOS DE LA BASE DE DATOS
   En XAMPP el usuario por defecto es "root" y la contrasena viene vacia.
   Si algun dia cambia la contrasena de MySQL, solo se edita aqui.
   --------------------------------------------------------------------------- */
define('DB_HOST', 'localhost');
define('DB_NAME', 'nubeturismo_db');
define('DB_USER', 'root');
define('DB_PASS', '');


/* ---------------------------------------------------------------------------
   2. RUTAS DEL PROYECTO

   BASE_PATH  = ruta REAL en el disco duro.
                Sirve para hacer require de los archivos PHP.
                Ejemplo: C:\xampp\htdocs\xampp\Proyecto\

   BASE_URL   = ruta que se escribe en el NAVEGADOR.
                Sirve para los enlaces (href) y los formularios (action).
                Ejemplo: /xampp/Proyecto/public

   Como todas las paginas estan dentro de la carpeta "public", se puede saber
   donde esta el proyecto usando $_SERVER['SCRIPT_NAME'], que trae la direccion
   de la pagina que se esta abriendo.

   Ejemplo paso a paso, si el usuario abre login.php:
       $_SERVER['SCRIPT_NAME'] = /xampp/Proyecto/public/login.php
       dirname(...)            = /xampp/Proyecto/public      <- esto es BASE_URL
       dirname(...) otra vez   = /xampp/Proyecto             <- carpeta raiz

   Se hace asi para que el proyecto funcione aunque se copie a otra carpeta,
   sin tener que andar cambiando las rutas a mano.
   --------------------------------------------------------------------------- */

// dirname(__DIR__) sube un nivel desde /config y da la carpeta del proyecto.
define('BASE_PATH', dirname(__DIR__));

// Carpeta public vista desde el navegador.
$rutaPublic = dirname($_SERVER['SCRIPT_NAME']);

// Carpeta raiz del proyecto vista desde el navegador.
// El rtrim quita la barra sobrante cuando el proyecto esta en la raiz de htdocs.
$rutaRaiz = rtrim(dirname($rutaPublic), '/');

define('BASE_URL', $rutaPublic);
define('RECURSOS_URL', $rutaRaiz . '/recursos');


/* ---------------------------------------------------------------------------
   3. CARGA DE LAS CLASES DEL SISTEMA

   El proyecto esta separado en capas. Cada capa tiene una responsabilidad:

     config/       -> conexion a la base de datos y funciones de apoyo
     modelos/      -> hablan con la base de datos (los SELECT, INSERT, UPDATE)
     servicios/    -> reglas del negocio (login, APIs externas, registro de errores)
     controladores/-> reciben lo que manda el usuario y deciden que hacer
     vistas/       -> el HTML que ve el usuario

   Se cargan con require_once, que incluye el archivo una sola vez aunque se
   pida varias veces. Se cargan en orden: primero lo mas basico y al final
   los controladores, porque los controladores usan a los demas.
   --------------------------------------------------------------------------- */

// --- Base: conexion y funciones que se usan en todo el sistema ---
require_once BASE_PATH . '/config/Database.php';
require_once BASE_PATH . '/config/funciones.php';

// --- Modelos: una clase por cada tabla importante ---
require_once BASE_PATH . '/modelos/Usuario.php';
require_once BASE_PATH . '/modelos/PasswordReset.php';
require_once BASE_PATH . '/modelos/Destino.php';
require_once BASE_PATH . '/modelos/Hotel.php';
require_once BASE_PATH . '/modelos/Actividad.php';
require_once BASE_PATH . '/modelos/Reservacion.php';
require_once BASE_PATH . '/modelos/Reporte.php';
require_once BASE_PATH . '/modelos/Bitacora.php';
require_once BASE_PATH . '/modelos/Resena.php';

// --- Servicios: logica que no pertenece a una sola tabla ---
require_once BASE_PATH . '/servicios/AuthService.php';
require_once BASE_PATH . '/servicios/ApiService.php';
require_once BASE_PATH . '/servicios/LoggerService.php';

// --- Controladores: uno por cada modulo del sistema ---
require_once BASE_PATH . '/controladores/AuthController.php';
require_once BASE_PATH . '/controladores/AdminDestinoController.php';
require_once BASE_PATH . '/controladores/AdminHotelController.php';
require_once BASE_PATH . '/controladores/AdminActividadController.php';
require_once BASE_PATH . '/controladores/AdminUsuarioController.php';
require_once BASE_PATH . '/controladores/AdminReservacionController.php';
require_once BASE_PATH . '/controladores/AdminReporteController.php';
require_once BASE_PATH . '/controladores/AdminBitacoraController.php';
require_once BASE_PATH . '/controladores/AdminResenaController.php';
require_once BASE_PATH . '/controladores/ClienteController.php';


/* ---------------------------------------------------------------------------
   4. SESION DEL USUARIO

   La sesion es lo que permite que el sistema "recuerde" quien inicio sesion
   mientras navega entre paginas.

   Se pregunta primero si ya hay una sesion abierta, porque si se llama
   session_start() dos veces PHP muestra un aviso de error.
   --------------------------------------------------------------------------- */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/* ---------------------------------------------------------------------------
   5. MANEJO DE ERRORES NO CONTROLADOS

   Si en alguna parte del sistema ocurre un error que no se atrapo con
   try/catch, PHP llama automaticamente a esta funcion.

   Lo importante es que al usuario NO se le muestra el error tecnico
   (eso seria inseguro y ademas se ve feo). El detalle se guarda en el
   archivo de log y al usuario solo se le da un mensaje amigable.
   --------------------------------------------------------------------------- */
function manejarErrorNoControlado($excepcion)
{
    // Se guarda el detalle tecnico en logs/app.log
    LoggerService::registrar('error', 'excepcion_no_controlada', $excepcion->getMessage());

    // Se le responde al usuario con un mensaje sencillo
    http_response_code(500);
    echo 'Ocurrio un error interno. Intenta nuevamente mas tarde.';
}

// Aqui se le dice a PHP cual funcion debe usar cuando ocurra un error.
set_exception_handler('manejarErrorNoControlado');
