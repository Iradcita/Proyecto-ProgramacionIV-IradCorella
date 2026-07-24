<?php

// Clase sencilla para no abrir una conexion nueva cada vez que se necesita.
class Database
{
    private static $conexion = null;

    public static function obtenerConexion()
    {
        if (self::$conexion === null) {
            try {
                $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
                self::$conexion = new PDO($dsn, DB_USER, DB_PASS);
                self::$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                die('Error al conectar con la base de datos: ' . $e->getMessage());
            }
        }

        return self::$conexion;
    }
}
