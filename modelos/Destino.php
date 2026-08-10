<?php

// Modelo de destinos turisticos: concentra las consultas usadas por el CRUD administrativo.
class Destino
{
    // Lista destinos y permite buscar por nombre o provincia.
    public static function listar($busqueda = '', $idProvincia = 0)
    {
        $conexion = Database::obtenerConexion();
        $busquedaLike = '%' . $busqueda . '%';

        $sql = "SELECT d.*, p.nombre AS provincia_nombre
                FROM destinos d
                INNER JOIN provincias p ON p.id_provincia = d.id_provincia
                WHERE (:busqueda = '' OR CONCAT(d.nombre, ' ', p.nombre) LIKE :busqueda_like)
                  AND (:id_provincia = 0 OR d.id_provincia = :id_provincia)
                ORDER BY d.estado DESC, d.nombre ASC";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':busqueda', $busqueda);
        $stmt->bindParam(':busqueda_like', $busquedaLike);
        $stmt->bindParam(':id_provincia', $idProvincia);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Devuelve solo destinos activos para combos de hoteles, actividades y reservaciones.
    public static function listarActivos()
    {
        $conexion = Database::obtenerConexion();

        $sql = "SELECT id_destino, nombre FROM destinos WHERE estado = 1 ORDER BY nombre";
        $stmt = $conexion->query($sql);

        return $stmt->fetchAll();
    }

    // Lista provincias para llenar el selector del formulario.
    public static function listarProvincias()
    {
        $conexion = Database::obtenerConexion();

        $sql = "SELECT id_provincia, nombre FROM provincias ORDER BY nombre";
        $stmt = $conexion->query($sql);

        return $stmt->fetchAll();
    }

    // Busca un destino por id para editarlo o validar que exista.
    public static function obtenerPorId($idDestino)
    {
        $conexion = Database::obtenerConexion();

        $sql = "SELECT * FROM destinos WHERE id_destino = :id_destino";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_destino', $idDestino);
        $stmt->execute();

        $fila = $stmt->fetch();

        return $fila ? $fila : null;
    }

    // Inserta un destino nuevo en la base de datos.
    public static function crear($idProvincia, $nombre, $descripcion, $imagenPrincipal, $latitud, $longitud, $estado)
    {
        $conexion = Database::obtenerConexion();

        $sql = "INSERT INTO destinos (id_provincia, nombre, descripcion, imagen_principal, latitud, longitud, estado)
                VALUES (:id_provincia, :nombre, :descripcion, :imagen_principal, :latitud, :longitud, :estado)";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_provincia', $idProvincia);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':imagen_principal', $imagenPrincipal);
        $stmt->bindParam(':latitud', $latitud);
        $stmt->bindParam(':longitud', $longitud);
        $stmt->bindParam(':estado', $estado);
        $stmt->execute();

        return $conexion->lastInsertId();
    }

    // Actualiza todos los campos editables de un destino.
    public static function actualizar($idDestino, $idProvincia, $nombre, $descripcion, $imagenPrincipal, $latitud, $longitud, $estado)
    {
        $conexion = Database::obtenerConexion();

        $sql = "UPDATE destinos
                SET id_provincia = :id_provincia,
                    nombre = :nombre,
                    descripcion = :descripcion,
                    imagen_principal = :imagen_principal,
                    latitud = :latitud,
                    longitud = :longitud,
                    estado = :estado
                WHERE id_destino = :id_destino";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_provincia', $idProvincia);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':imagen_principal', $imagenPrincipal);
        $stmt->bindParam(':latitud', $latitud);
        $stmt->bindParam(':longitud', $longitud);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':id_destino', $idDestino);
        $stmt->execute();
    }

    // Desactiva el destino para conservar integridad con hoteles, actividades y reservas.
    public static function desactivar($idDestino)
    {
        $conexion = Database::obtenerConexion();

        $sql = "UPDATE destinos SET estado = 0 WHERE id_destino = :id_destino";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_destino', $idDestino);
        $stmt->execute();
    }
}
