<?php

// Modelo de reportes administrativos con consultas agregadas del sistema.
class Reporte
{
    // Cuenta reservaciones e ingresos agrupados por destino.
    public static function reservacionesPorDestino()
    {
        $conexion = Database::obtenerConexion();

        $sql = "SELECT COALESCE(d.nombre, 'Sin destino') AS destino,
                       COUNT(r.id_reservacion) AS total_reservaciones,
                       SUM(CASE WHEN r.estado <> 'cancelada' THEN r.total ELSE 0 END) AS ingresos
                FROM reservaciones r
                LEFT JOIN reservacion_hotel rh ON rh.id_reservacion = r.id_reservacion
                LEFT JOIN hoteles h ON h.id_hotel = rh.id_hotel
                LEFT JOIN destinos d ON d.id_destino = h.id_destino
                GROUP BY d.nombre
                ORDER BY total_reservaciones DESC, destino ASC";

        return $conexion->query($sql)->fetchAll();
    }

    // Muestra los hoteles con mas reservas asociadas.
    public static function hotelesMasReservados()
    {
        $conexion = Database::obtenerConexion();

        $sql = "SELECT h.nombre AS hotel,
                       d.nombre AS destino,
                       COUNT(rh.id_reservacion_hotel) AS total_reservas,
                       SUM(rh.subtotal) AS ingresos_hotel
                FROM reservacion_hotel rh
                INNER JOIN hoteles h ON h.id_hotel = rh.id_hotel
                INNER JOIN destinos d ON d.id_destino = h.id_destino
                GROUP BY h.id_hotel, h.nombre, d.nombre
                ORDER BY total_reservas DESC, ingresos_hotel DESC
                LIMIT 10";

        return $conexion->query($sql)->fetchAll();
    }

    // Lista actividades segun solicitudes y cantidad de personas.
    public static function actividadesMasSolicitadas()
    {
        $conexion = Database::obtenerConexion();

        $sql = "SELECT a.nombre AS actividad,
                       d.nombre AS destino,
                       COUNT(ra.id_reservacion_actividad) AS total_solicitudes,
                       SUM(ra.cantidad_personas) AS total_personas,
                       SUM(ra.subtotal) AS ingresos_actividad
                FROM reservacion_actividad ra
                INNER JOIN actividades a ON a.id_actividad = ra.id_actividad
                INNER JOIN destinos d ON d.id_destino = a.id_destino
                GROUP BY a.id_actividad, a.nombre, d.nombre
                ORDER BY total_solicitudes DESC, total_personas DESC
                LIMIT 10";

        return $conexion->query($sql)->fetchAll();
    }

    // Resume usuarios registrados por rol y estado.
    public static function usuariosRegistrados()
    {
        $conexion = Database::obtenerConexion();

        $sql = "SELECT r.nombre AS rol,
                       u.estado,
                       COUNT(u.id_usuario) AS total_usuarios
                FROM usuarios u
                INNER JOIN roles r ON r.id_rol = u.id_rol
                GROUP BY r.nombre, u.estado
                ORDER BY r.nombre, u.estado";

        return $conexion->query($sql)->fetchAll();
    }

    // Agrupa reservas por fecha de creacion.
    public static function reservacionesPorFecha()
    {
        $conexion = Database::obtenerConexion();

        $sql = "SELECT DATE(fecha_reserva) AS fecha,
                       COUNT(id_reservacion) AS total_reservaciones,
                       SUM(CASE WHEN estado <> 'cancelada' THEN total ELSE 0 END) AS ingresos
                FROM reservaciones
                GROUP BY DATE(fecha_reserva)
                ORDER BY fecha DESC
                LIMIT 30";

        return $conexion->query($sql)->fetchAll();
    }

    // Calcula ingresos por estado y un total estimado sin canceladas.
    public static function ingresosEstimados()
    {
        $conexion = Database::obtenerConexion();

        $sql = "SELECT estado,
                       COUNT(id_reservacion) AS total_reservaciones,
                       SUM(total) AS total_ingresos
                FROM reservaciones
                GROUP BY estado
                ORDER BY total_ingresos DESC";

        return $conexion->query($sql)->fetchAll();
    }

    // Devuelve totales generales para las tarjetas superiores del reporte.
    public static function resumenGeneral()
    {
        $conexion = Database::obtenerConexion();

        $sql = "SELECT
                    (SELECT COUNT(*) FROM reservaciones) AS total_reservaciones,
                    (SELECT COUNT(*) FROM usuarios) AS total_usuarios,
                    (SELECT COUNT(*) FROM hoteles WHERE estado = 1) AS hoteles_activos,
                    (SELECT COUNT(*) FROM actividades WHERE estado = 1) AS actividades_activas,
                    (SELECT COALESCE(SUM(total), 0) FROM reservaciones WHERE estado <> 'cancelada') AS ingresos_estimados";

        $stmt = $conexion->query($sql);

        return $stmt->fetch();
    }

    // Busca un destino activo con coordenadas para consultar clima.
    public static function destinoConCoordenadas()
    {
        $conexion = Database::obtenerConexion();

        $sql = "SELECT nombre, latitud, longitud
                FROM destinos
                WHERE estado = 1
                  AND latitud IS NOT NULL
                  AND longitud IS NOT NULL
                ORDER BY nombre
                LIMIT 1";

        $stmt = $conexion->query($sql);
        $fila = $stmt->fetch();

        if ($fila) {
            return $fila;
        }

        return array(
            'nombre' => 'San Jose',
            'latitud' => 9.9281,
            'longitud' => -84.0907,
        );
    }
}
