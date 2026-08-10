<?php

// Modelo de actividades turisticas: contiene el CRUD de experiencias por destino.
class Actividad
{
    // Lista actividades junto al destino asociado.
    public static function listar($busqueda = '', $idDestino = 0)
    {
        $conexion = Database::obtenerConexion();
        $busquedaLike = '%' . $busqueda . '%';

        $sql = "SELECT a.*, d.nombre AS destino_nombre
                FROM actividades a
                INNER JOIN destinos d ON d.id_destino = a.id_destino
                WHERE (:busqueda = '' OR CONCAT(a.nombre, ' ', d.nombre) LIKE :busqueda_like)
                  AND (:id_destino = 0 OR a.id_destino = :id_destino)
                ORDER BY a.estado DESC, a.nombre ASC";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':busqueda', $busqueda);
        $stmt->bindParam(':busqueda_like', $busquedaLike);
        $stmt->bindParam(':id_destino', $idDestino);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Devuelve actividades activas para seleccionar en reservaciones.
    public static function listarActivas()
    {
        $conexion = Database::obtenerConexion();

        $sql = "SELECT a.id_actividad, a.nombre, a.precio, d.nombre AS destino_nombre
                FROM actividades a
                INNER JOIN destinos d ON d.id_destino = a.id_destino
                WHERE a.estado = 1 AND d.estado = 1
                ORDER BY d.nombre, a.nombre";
        $stmt = $conexion->query($sql);

        return $stmt->fetchAll();
    }

    // Busca actividades activas para clientes por texto y destino.
    public static function buscarPublicas($busqueda = '', $idDestino = 0)
    {
        $conexion = Database::obtenerConexion();
        $busquedaLike = '%' . $busqueda . '%';

        $sql = "SELECT a.*, d.nombre AS destino_nombre
                FROM actividades a
                INNER JOIN destinos d ON d.id_destino = a.id_destino
                WHERE a.estado = 1
                  AND d.estado = 1
                  AND (:busqueda = '' OR CONCAT(a.nombre, ' ', d.nombre, ' ', a.descripcion) LIKE :busqueda_like)
                  AND (:id_destino = 0 OR a.id_destino = :id_destino)
                ORDER BY a.precio ASC, a.nombre ASC";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':busqueda', $busqueda);
        $stmt->bindParam(':busqueda_like', $busquedaLike);
        $stmt->bindParam(':id_destino', $idDestino);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Obtiene una actividad activa para validar selecciones del cliente.
    public static function obtenerActivaPorId($idActividad)
    {
        $conexion = Database::obtenerConexion();

        $sql = "SELECT a.*
                FROM actividades a
                INNER JOIN destinos d ON d.id_destino = a.id_destino
                WHERE a.id_actividad = :id_actividad AND a.estado = 1 AND d.estado = 1";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_actividad', $idActividad);
        $stmt->execute();

        $fila = $stmt->fetch();

        return $fila ? $fila : null;
    }

    // Obtiene una actividad por id.
    public static function obtenerPorId($idActividad)
    {
        $conexion = Database::obtenerConexion();

        $sql = "SELECT * FROM actividades WHERE id_actividad = :id_actividad";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_actividad', $idActividad);
        $stmt->execute();

        $fila = $stmt->fetch();

        return $fila ? $fila : null;
    }

    // Inserta una actividad nueva.
    public static function crear($idDestino, $nombre, $descripcion, $precio, $duracionMinutos, $cupoMaximo, $imagen, $estado)
    {
        $conexion = Database::obtenerConexion();

        $sql = "INSERT INTO actividades (id_destino, nombre, descripcion, precio, duracion_minutos, cupo_maximo, imagen, estado)
                VALUES (:id_destino, :nombre, :descripcion, :precio, :duracion_minutos, :cupo_maximo, :imagen, :estado)";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_destino', $idDestino);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':duracion_minutos', $duracionMinutos);
        $stmt->bindParam(':cupo_maximo', $cupoMaximo);
        $stmt->bindParam(':imagen', $imagen);
        $stmt->bindParam(':estado', $estado);
        $stmt->execute();

        return $conexion->lastInsertId();
    }

    // Actualiza todos los datos editables de una actividad.
    public static function actualizar($idActividad, $idDestino, $nombre, $descripcion, $precio, $duracionMinutos, $cupoMaximo, $imagen, $estado)
    {
        $conexion = Database::obtenerConexion();

        $sql = "UPDATE actividades
                SET id_destino = :id_destino,
                    nombre = :nombre,
                    descripcion = :descripcion,
                    precio = :precio,
                    duracion_minutos = :duracion_minutos,
                    cupo_maximo = :cupo_maximo,
                    imagen = :imagen,
                    estado = :estado
                WHERE id_actividad = :id_actividad";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_destino', $idDestino);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':duracion_minutos', $duracionMinutos);
        $stmt->bindParam(':cupo_maximo', $cupoMaximo);
        $stmt->bindParam(':imagen', $imagen);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':id_actividad', $idActividad);
        $stmt->execute();
    }

    // Desactiva la actividad para no perder el historial.
    public static function desactivar($idActividad)
    {
        $conexion = Database::obtenerConexion();

        $sql = "UPDATE actividades SET estado = 0 WHERE id_actividad = :id_actividad";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_actividad', $idActividad);
        $stmt->execute();
    }
}
