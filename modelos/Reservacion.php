<?php

// Modelo de reservaciones: maneja reservas, hotel asociado y actividades seleccionadas.
class Reservacion
{
    // Lista todas las reservaciones con datos resumidos para administracion.
    public static function listarTodas($busqueda = '', $estado = '')
    {
        $conexion = Database::obtenerConexion();
        $busquedaLike = '%' . $busqueda . '%';

        $sql = "SELECT r.*,
                       CONCAT(u.nombre, ' ', u.apellidos) AS cliente_nombre,
                       u.correo AS cliente_correo,
                       h.nombre AS hotel_nombre,
                       COUNT(ra.id_reservacion_actividad) AS total_actividades
                FROM reservaciones r
                INNER JOIN usuarios u ON u.id_usuario = r.id_usuario
                LEFT JOIN reservacion_hotel rh ON rh.id_reservacion = r.id_reservacion
                LEFT JOIN hoteles h ON h.id_hotel = rh.id_hotel
                LEFT JOIN reservacion_actividad ra ON ra.id_reservacion = r.id_reservacion
                WHERE (:busqueda = '' OR CONCAT(r.codigo, ' ', u.nombre, ' ', u.apellidos, ' ', u.correo) LIKE :busqueda_like)
                  AND (:estado = '' OR r.estado = :estado)
                GROUP BY r.id_reservacion, u.nombre, u.apellidos, u.correo, h.nombre
                ORDER BY r.fecha_reserva DESC";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':busqueda', $busqueda);
        $stmt->bindParam(':busqueda_like', $busquedaLike);
        $stmt->bindParam(':estado', $estado);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Lista reservaciones del cliente conectado con resumen de hotel y actividades.
    public static function listarPorUsuario($idUsuario)
    {
        $conexion = Database::obtenerConexion();

        $sql = "SELECT r.*,
                       h.nombre AS hotel_nombre,
                       d.nombre AS destino_nombre,
                       COUNT(ra.id_reservacion_actividad) AS total_actividades
                FROM reservaciones r
                LEFT JOIN reservacion_hotel rh ON rh.id_reservacion = r.id_reservacion
                LEFT JOIN hoteles h ON h.id_hotel = rh.id_hotel
                LEFT JOIN destinos d ON d.id_destino = h.id_destino
                LEFT JOIN reservacion_actividad ra ON ra.id_reservacion = r.id_reservacion
                WHERE r.id_usuario = :id_usuario
                GROUP BY r.id_reservacion, h.nombre, d.nombre
                ORDER BY r.fecha_reserva DESC";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_usuario', $idUsuario);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Obtiene una reservacion del usuario conectado con datos de hotel y destino.
    public static function obtenerDetallePorUsuario($idReservacion, $idUsuario)
    {
        $conexion = Database::obtenerConexion();

        $sql = "SELECT r.*,
                       rh.cantidad_habitaciones,
                       rh.precio_noche_aplicado,
                       rh.subtotal AS subtotal_hotel,
                       h.nombre AS hotel_nombre,
                       h.direccion AS hotel_direccion,
                       h.telefono AS hotel_telefono,
                       d.nombre AS destino_nombre
                FROM reservaciones r
                LEFT JOIN reservacion_hotel rh ON rh.id_reservacion = r.id_reservacion
                LEFT JOIN hoteles h ON h.id_hotel = rh.id_hotel
                LEFT JOIN destinos d ON d.id_destino = h.id_destino
                WHERE r.id_reservacion = :id_reservacion
                  AND r.id_usuario = :id_usuario";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_reservacion', $idReservacion);
        $stmt->bindParam(':id_usuario', $idUsuario);
        $stmt->execute();

        $fila = $stmt->fetch();

        return $fila ? $fila : null;
    }

    // Lista las actividades detalladas de una reservacion del usuario conectado.
    public static function listarActividadesDetallePorUsuario($idReservacion, $idUsuario)
    {
        $conexion = Database::obtenerConexion();

        $sql = "SELECT ra.*,
                       a.nombre AS actividad_nombre,
                       d.nombre AS destino_nombre
                FROM reservacion_actividad ra
                INNER JOIN reservaciones r ON r.id_reservacion = ra.id_reservacion
                INNER JOIN actividades a ON a.id_actividad = ra.id_actividad
                INNER JOIN destinos d ON d.id_destino = a.id_destino
                WHERE ra.id_reservacion = :id_reservacion
                  AND r.id_usuario = :id_usuario
                ORDER BY ra.fecha_hora ASC";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_reservacion', $idReservacion);
        $stmt->bindParam(':id_usuario', $idUsuario);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Obtiene los datos principales de una reservacion.
    public static function obtenerPorId($idReservacion)
    {
        $conexion = Database::obtenerConexion();

        $sql = "SELECT * FROM reservaciones WHERE id_reservacion = :id_reservacion";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_reservacion', $idReservacion);
        $stmt->execute();

        $fila = $stmt->fetch();

        return $fila ? $fila : null;
    }

    // Devuelve el hotel vinculado a una reservacion.
    public static function obtenerHotel($idReservacion)
    {
        $conexion = Database::obtenerConexion();

        $sql = "SELECT * FROM reservacion_hotel WHERE id_reservacion = :id_reservacion LIMIT 1";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_reservacion', $idReservacion);
        $stmt->execute();

        $fila = $stmt->fetch();

        return $fila ? $fila : null;
    }

    // Devuelve las actividades vinculadas a una reservacion.
    public static function obtenerActividades($idReservacion)
    {
        $conexion = Database::obtenerConexion();

        $sql = "SELECT * FROM reservacion_actividad WHERE id_reservacion = :id_reservacion";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_reservacion', $idReservacion);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Crea una reservacion completa usando transaccion para mantener consistencia.
    public static function crearCompleta($idUsuario, $fechaInicio, $fechaFin, $cantidadPersonas, $estado, $observaciones, $idHotel, $cantidadHabitaciones, $actividadesIds)
    {
        $conexion = Database::obtenerConexion();

        try {
            $conexion->beginTransaction();

            $codigo = self::generarCodigo($conexion);
            $total = self::calcularTotal($fechaInicio, $fechaFin, $cantidadPersonas, $idHotel, $cantidadHabitaciones, $actividadesIds);

            $sql = "INSERT INTO reservaciones (id_usuario, codigo, fecha_inicio, fecha_fin, cantidad_personas, estado, total, observaciones)
                    VALUES (:id_usuario, :codigo, :fecha_inicio, :fecha_fin, :cantidad_personas, :estado, :total, :observaciones)";

            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(':id_usuario', $idUsuario);
            $stmt->bindParam(':codigo', $codigo);
            $stmt->bindParam(':fecha_inicio', $fechaInicio);
            $stmt->bindParam(':fecha_fin', $fechaFin);
            $stmt->bindParam(':cantidad_personas', $cantidadPersonas);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':total', $total);
            $stmt->bindParam(':observaciones', $observaciones);
            $stmt->execute();

            $idReservacion = $conexion->lastInsertId();
            self::guardarHotel($idReservacion, $idHotel, $cantidadHabitaciones, $fechaInicio, $fechaFin);
            self::guardarActividades($idReservacion, $actividadesIds, $fechaInicio, $cantidadPersonas);

            $conexion->commit();

            return $idReservacion;
        } catch (Exception $e) {
            $conexion->rollBack();
            throw $e;
        }
    }

    // Actualiza una reservacion y reconstruye sus detalles de hotel y actividades.
    public static function actualizarCompleta($idReservacion, $idUsuario, $fechaInicio, $fechaFin, $cantidadPersonas, $estado, $observaciones, $idHotel, $cantidadHabitaciones, $actividadesIds)
    {
        $conexion = Database::obtenerConexion();

        try {
            $conexion->beginTransaction();

            $total = self::calcularTotal($fechaInicio, $fechaFin, $cantidadPersonas, $idHotel, $cantidadHabitaciones, $actividadesIds);

            $sql = "UPDATE reservaciones
                    SET id_usuario = :id_usuario,
                        fecha_inicio = :fecha_inicio,
                        fecha_fin = :fecha_fin,
                        cantidad_personas = :cantidad_personas,
                        estado = :estado,
                        total = :total,
                        observaciones = :observaciones
                    WHERE id_reservacion = :id_reservacion";

            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(':id_usuario', $idUsuario);
            $stmt->bindParam(':fecha_inicio', $fechaInicio);
            $stmt->bindParam(':fecha_fin', $fechaFin);
            $stmt->bindParam(':cantidad_personas', $cantidadPersonas);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':total', $total);
            $stmt->bindParam(':observaciones', $observaciones);
            $stmt->bindParam(':id_reservacion', $idReservacion);
            $stmt->execute();

            self::eliminarDetalles($idReservacion);
            self::guardarHotel($idReservacion, $idHotel, $cantidadHabitaciones, $fechaInicio, $fechaFin);
            self::guardarActividades($idReservacion, $actividadesIds, $fechaInicio, $cantidadPersonas);

            $conexion->commit();
        } catch (Exception $e) {
            $conexion->rollBack();
            throw $e;
        }
    }

    // Marca una reservacion como cancelada para conservar el historial.
    public static function cancelar($idReservacion)
    {
        $conexion = Database::obtenerConexion();

        $sql = "UPDATE reservaciones SET estado = 'cancelada' WHERE id_reservacion = :id_reservacion";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_reservacion', $idReservacion);
        $stmt->execute();
    }

    // Calcula el total tomando noches de hotel y precio de actividades por persona.
    private static function calcularTotal($fechaInicio, $fechaFin, $cantidadPersonas, $idHotel, $cantidadHabitaciones, $actividadesIds)
    {
        $hotel = Hotel::obtenerPorId($idHotel);

        if (!$hotel) {
            throw new Exception('El hotel seleccionado no existe.');
        }

        $noches = max(1, (int) ((strtotime($fechaFin) - strtotime($fechaInicio)) / 86400));
        $total = ((float) $hotel['precio_noche']) * $noches * $cantidadHabitaciones;

        foreach ($actividadesIds as $idActividad) {
            $actividad = Actividad::obtenerPorId($idActividad);
            if ($actividad) {
                $total += ((float) $actividad['precio']) * $cantidadPersonas;
            }
        }

        return $total;
    }

    // Inserta o reinserta el detalle de hotel con el precio aplicado.
    private static function guardarHotel($idReservacion, $idHotel, $cantidadHabitaciones, $fechaInicio, $fechaFin)
    {
        $conexion = Database::obtenerConexion();
        $hotel = Hotel::obtenerPorId($idHotel);

        if (!$hotel) {
            throw new Exception('El hotel seleccionado no existe.');
        }

        $noches = max(1, (int) ((strtotime($fechaFin) - strtotime($fechaInicio)) / 86400));
        $precioNoche = (float) $hotel['precio_noche'];
        $subtotal = $precioNoche * $noches * $cantidadHabitaciones;

        $sql = "INSERT INTO reservacion_hotel (id_reservacion, id_hotel, cantidad_habitaciones, precio_noche_aplicado, subtotal)
                VALUES (:id_reservacion, :id_hotel, :cantidad_habitaciones, :precio_noche_aplicado, :subtotal)";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_reservacion', $idReservacion);
        $stmt->bindParam(':id_hotel', $idHotel);
        $stmt->bindParam(':cantidad_habitaciones', $cantidadHabitaciones);
        $stmt->bindParam(':precio_noche_aplicado', $precioNoche);
        $stmt->bindParam(':subtotal', $subtotal);
        $stmt->execute();
    }

    // Inserta el detalle de cada actividad seleccionada.
    private static function guardarActividades($idReservacion, $actividadesIds, $fechaInicio, $cantidadPersonas)
    {
        $conexion = Database::obtenerConexion();
        $fechaHora = $fechaInicio . ' 08:00:00';

        foreach ($actividadesIds as $idActividad) {
            $actividad = Actividad::obtenerPorId($idActividad);
            if (!$actividad) {
                continue;
            }

            $precio = (float) $actividad['precio'];
            $subtotal = $precio * $cantidadPersonas;

            $sql = "INSERT INTO reservacion_actividad (id_reservacion, id_actividad, fecha_hora, cantidad_personas, precio_persona_aplicado, subtotal)
                    VALUES (:id_reservacion, :id_actividad, :fecha_hora, :cantidad_personas, :precio_persona_aplicado, :subtotal)";

            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(':id_reservacion', $idReservacion);
            $stmt->bindParam(':id_actividad', $idActividad);
            $stmt->bindParam(':fecha_hora', $fechaHora);
            $stmt->bindParam(':cantidad_personas', $cantidadPersonas);
            $stmt->bindParam(':precio_persona_aplicado', $precio);
            $stmt->bindParam(':subtotal', $subtotal);
            $stmt->execute();
        }
    }

    // Limpia detalles para reconstruirlos al editar una reservacion.
    private static function eliminarDetalles($idReservacion)
    {
        $conexion = Database::obtenerConexion();

        $stmt = $conexion->prepare("DELETE FROM reservacion_actividad WHERE id_reservacion = :id_reservacion");
        $stmt->bindParam(':id_reservacion', $idReservacion);
        $stmt->execute();

        $stmt = $conexion->prepare("DELETE FROM reservacion_hotel WHERE id_reservacion = :id_reservacion");
        $stmt->bindParam(':id_reservacion', $idReservacion);
        $stmt->execute();
    }

    // Genera un codigo legible para identificar cada reservacion.
    private static function generarCodigo($conexion)
    {
        $sql = "SELECT COUNT(*) + 1 AS siguiente FROM reservaciones";
        $stmt = $conexion->query($sql);
        $fila = $stmt->fetch();

        return 'ITR-' . date('Y') . '-' . str_pad((string) $fila['siguiente'], 6, '0', STR_PAD_LEFT);
    }
}
