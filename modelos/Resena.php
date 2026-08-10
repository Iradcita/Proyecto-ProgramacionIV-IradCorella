<?php

// Modelo para comentarios y calificaciones de destinos.
class Resena
{
    // Lista resenas aprobadas de un destino para el catalogo publico.
    public static function listarAprobadasPorDestino($idDestino)
    {
        $conexion = Database::obtenerConexion();

        $sql = "SELECT rd.*, CONCAT(u.nombre, ' ', u.apellidos) AS usuario_nombre
                FROM resenas_destinos rd
                INNER JOIN usuarios u ON u.id_usuario = rd.id_usuario
                WHERE rd.id_destino = :id_destino
                  AND rd.estado = 'aprobada'
                ORDER BY rd.fecha DESC";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_destino', $idDestino);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Busca la resena del usuario para evitar duplicados por destino.
    public static function obtenerDeUsuarioDestino($idUsuario, $idDestino)
    {
        $conexion = Database::obtenerConexion();

        $sql = "SELECT * FROM resenas_destinos
                WHERE id_usuario = :id_usuario AND id_destino = :id_destino";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_usuario', $idUsuario);
        $stmt->bindParam(':id_destino', $idDestino);
        $stmt->execute();

        $fila = $stmt->fetch();

        return $fila ? $fila : null;
    }

    // Crea o actualiza la resena del usuario y la deja pendiente.
    public static function guardar($idUsuario, $idDestino, $calificacion, $comentario)
    {
        $conexion = Database::obtenerConexion();
        $existente = self::obtenerDeUsuarioDestino($idUsuario, $idDestino);

        if ($existente) {
            $idResena = $existente['id_resena'];
            $sql = "UPDATE resenas_destinos
                    SET calificacion = :calificacion,
                        comentario = :comentario,
                        estado = 'pendiente',
                        fecha = NOW()
                    WHERE id_resena = :id_resena";

            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(':calificacion', $calificacion);
            $stmt->bindParam(':comentario', $comentario);
            $stmt->bindParam(':id_resena', $idResena);
            $stmt->execute();

            return $idResena;
        }

        $sql = "INSERT INTO resenas_destinos (id_usuario, id_destino, calificacion, comentario, estado)
                VALUES (:id_usuario, :id_destino, :calificacion, :comentario, 'pendiente')";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_usuario', $idUsuario);
        $stmt->bindParam(':id_destino', $idDestino);
        $stmt->bindParam(':calificacion', $calificacion);
        $stmt->bindParam(':comentario', $comentario);
        $stmt->execute();

        return $conexion->lastInsertId();
    }

    // Lista resenas para moderacion administrativa.
    public static function listarAdmin($estado = '')
    {
        $conexion = Database::obtenerConexion();

        $sql = "SELECT rd.*, d.nombre AS destino_nombre, CONCAT(u.nombre, ' ', u.apellidos) AS usuario_nombre, u.correo
                FROM resenas_destinos rd
                INNER JOIN destinos d ON d.id_destino = rd.id_destino
                INNER JOIN usuarios u ON u.id_usuario = rd.id_usuario
                WHERE (:estado = '' OR rd.estado = :estado)
                ORDER BY rd.fecha DESC";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':estado', $estado);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Cambia el estado de moderacion de una resena.
    public static function cambiarEstado($idResena, $estado)
    {
        $conexion = Database::obtenerConexion();

        $sql = "UPDATE resenas_destinos SET estado = :estado WHERE id_resena = :id_resena";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':id_resena', $idResena);
        $stmt->execute();
    }
}
