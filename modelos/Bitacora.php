<?php

// Modelo para registrar y consultar acciones importantes del sistema.
class Bitacora
{
    // Guarda una accion relacionada con un usuario y un registro.
    public static function registrar($idUsuario, $accion, $tablaAfectada = null, $idRegistroAfectado = null)
    {
        if ($idUsuario <= 0) {
            return;
        }

        $conexion = Database::obtenerConexion();

        $sql = "INSERT INTO bitacora_acciones (id_usuario, accion, tabla_afectada, id_registro_afectado)
                VALUES (:id_usuario, :accion, :tabla_afectada, :id_registro_afectado)";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_usuario', $idUsuario);
        $stmt->bindParam(':accion', $accion);
        $stmt->bindParam(':tabla_afectada', $tablaAfectada);
        $stmt->bindParam(':id_registro_afectado', $idRegistroAfectado);
        $stmt->execute();
    }

    // Lista acciones recientes para auditoria administrativa.
    public static function listar($busqueda = '')
    {
        $conexion = Database::obtenerConexion();
        $busquedaLike = '%' . $busqueda . '%';

        $sql = "SELECT b.*, CONCAT(u.nombre, ' ', u.apellidos) AS usuario_nombre, u.correo
                FROM bitacora_acciones b
                INNER JOIN usuarios u ON u.id_usuario = b.id_usuario
                WHERE (:busqueda = '' OR CONCAT(b.accion, ' ', COALESCE(b.tabla_afectada, ''), ' ', u.nombre, ' ', u.apellidos, ' ', u.correo) LIKE :busqueda_like)
                ORDER BY b.fecha DESC
                LIMIT 100";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':busqueda', $busqueda);
        $stmt->bindParam(':busqueda_like', $busquedaLike);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
