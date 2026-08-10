<?php

// Modelo de hoteles: administra el acceso a datos de hospedajes.
class Hotel
{
    // Lista hoteles con el nombre del destino para la tabla administrativa.
    public static function listar($busqueda = '', $idDestino = 0)
    {
        $conexion = Database::obtenerConexion();
        $busquedaLike = '%' . $busqueda . '%';

        $sql = "SELECT h.*, d.nombre AS destino_nombre
                FROM hoteles h
                INNER JOIN destinos d ON d.id_destino = h.id_destino
                WHERE (:busqueda = '' OR CONCAT(h.nombre, ' ', d.nombre) LIKE :busqueda_like)
                  AND (:id_destino = 0 OR h.id_destino = :id_destino)
                ORDER BY h.estado DESC, h.nombre ASC";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':busqueda', $busqueda);
        $stmt->bindParam(':busqueda_like', $busquedaLike);
        $stmt->bindParam(':id_destino', $idDestino);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Devuelve hoteles activos para escogerlos en reservaciones.
    public static function listarActivos()
    {
        $conexion = Database::obtenerConexion();

        $sql = "SELECT h.id_hotel, h.nombre, h.precio_noche, d.nombre AS destino_nombre
                FROM hoteles h
                INNER JOIN destinos d ON d.id_destino = h.id_destino
                WHERE h.estado = 1 AND d.estado = 1
                ORDER BY d.nombre, h.nombre";
        $stmt = $conexion->query($sql);

        return $stmt->fetchAll();
    }

    // Busca un hotel por id.
    public static function obtenerPorId($idHotel)
    {
        $conexion = Database::obtenerConexion();

        $sql = "SELECT * FROM hoteles WHERE id_hotel = :id_hotel";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_hotel', $idHotel);
        $stmt->execute();

        $fila = $stmt->fetch();

        return $fila ? $fila : null;
    }

    // Crea un hotel nuevo.
    public static function crear($idDestino, $nombre, $categoria, $direccion, $telefono, $correo, $precioNoche, $cantidadHabitaciones, $descripcion, $imagen, $estado)
    {
        $conexion = Database::obtenerConexion();

        $sql = "INSERT INTO hoteles (id_destino, nombre, categoria, direccion, telefono, correo, precio_noche, cantidad_habitaciones, descripcion, imagen, estado)
                VALUES (:id_destino, :nombre, :categoria, :direccion, :telefono, :correo, :precio_noche, :cantidad_habitaciones, :descripcion, :imagen, :estado)";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_destino', $idDestino);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':categoria', $categoria);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':precio_noche', $precioNoche);
        $stmt->bindParam(':cantidad_habitaciones', $cantidadHabitaciones);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':imagen', $imagen);
        $stmt->bindParam(':estado', $estado);
        $stmt->execute();

        return $conexion->lastInsertId();
    }

    // Actualiza la informacion del hotel seleccionado.
    public static function actualizar($idHotel, $idDestino, $nombre, $categoria, $direccion, $telefono, $correo, $precioNoche, $cantidadHabitaciones, $descripcion, $imagen, $estado)
    {
        $conexion = Database::obtenerConexion();

        $sql = "UPDATE hoteles
                SET id_destino = :id_destino,
                    nombre = :nombre,
                    categoria = :categoria,
                    direccion = :direccion,
                    telefono = :telefono,
                    correo = :correo,
                    precio_noche = :precio_noche,
                    cantidad_habitaciones = :cantidad_habitaciones,
                    descripcion = :descripcion,
                    imagen = :imagen,
                    estado = :estado
                WHERE id_hotel = :id_hotel";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_destino', $idDestino);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':categoria', $categoria);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':precio_noche', $precioNoche);
        $stmt->bindParam(':cantidad_habitaciones', $cantidadHabitaciones);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':imagen', $imagen);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':id_hotel', $idHotel);
        $stmt->execute();
    }

    // Desactiva el hotel en vez de borrarlo fisicamente.
    public static function desactivar($idHotel)
    {
        $conexion = Database::obtenerConexion();

        $sql = "UPDATE hoteles SET estado = 0 WHERE id_hotel = :id_hotel";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_hotel', $idHotel);
        $stmt->execute();
    }
}
