<?php

// Modelo de usuarios: guarda los datos y tambien las consultas a la tabla,
// para no tener que crear una clase aparte solo para eso.
class Usuario
{
    // Lista usuarios con su rol para la pantalla de administracion.
    public static function listarTodos($busqueda = '')
    {
        $conexion = Database::obtenerConexion();
        $busquedaLike = '%' . $busqueda . '%';

        $sql = "SELECT u.id_usuario, u.id_rol, u.nombre, u.apellidos, u.correo, u.telefono,
                       u.foto_url, u.estado, u.fecha_registro, r.nombre AS rol_nombre
                FROM usuarios u
                INNER JOIN roles r ON r.id_rol = u.id_rol
                WHERE (:busqueda = '' OR CONCAT(u.nombre, ' ', u.apellidos, ' ', u.correo) LIKE :busqueda_like)
                ORDER BY u.fecha_registro DESC";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':busqueda', $busqueda);
        $stmt->bindParam(':busqueda_like', $busquedaLike);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Obtiene un usuario especifico para editarlo desde administracion.
    public static function obtenerPorId($idUsuario)
    {
        $conexion = Database::obtenerConexion();

        $sql = "SELECT u.*, r.nombre AS rol_nombre
                FROM usuarios u
                INNER JOIN roles r ON r.id_rol = u.id_rol
                WHERE u.id_usuario = :id_usuario";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_usuario', $idUsuario);
        $stmt->execute();

        $fila = $stmt->fetch();

        return $fila ? $fila : null;
    }

    // Devuelve los roles activos que se pueden asignar a un usuario.
    public static function listarRoles()
    {
        $conexion = Database::obtenerConexion();

        $sql = "SELECT id_rol, nombre FROM roles WHERE estado = 1 ORDER BY nombre";
        $stmt = $conexion->query($sql);

        return $stmt->fetchAll();
    }

    // Lista clientes activos para asignarlos a una reservacion.
    public static function listarClientesActivos()
    {
        $conexion = Database::obtenerConexion();

        $sql = "SELECT id_usuario, nombre, apellidos, correo
                FROM usuarios
                WHERE id_rol = 2 AND estado = 'activo'
                ORDER BY nombre, apellidos";
        $stmt = $conexion->query($sql);

        return $stmt->fetchAll();
    }

    public static function buscarPorCorreo($correo)
    {
        $conexion = Database::obtenerConexion();

        $sql = "SELECT u.*, r.nombre AS rol_nombre
                FROM usuarios u
                INNER JOIN roles r ON r.id_rol = u.id_rol
                WHERE u.correo = :correo";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();

        $fila = $stmt->fetch();

        return $fila ? $fila : null;
    }

    public static function existeCorreo($correo)
    {
        $conexion = Database::obtenerConexion();

        $sql = "SELECT id_usuario FROM usuarios WHERE correo = :correo";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();

        return $stmt->fetch() !== false;
    }

    public static function crear($nombre, $apellidos, $correo, $telefono, $passwordHash)
    {
        $conexion = Database::obtenerConexion();

        // El registro publico siempre crea clientes (id_rol = 2)
        $sql = "INSERT INTO usuarios (id_rol, nombre, apellidos, correo, telefono, password_hash, estado)
                VALUES (2, :nombre, :apellidos, :correo, :telefono, :password_hash, 'activo')";
        //No corremos el riesgo de inyeccion SQL porque primero nos preparamos
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellidos', $apellidos);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':password_hash', $passwordHash);
        $stmt->execute();

        return $conexion->lastInsertId();
    }

    // Crea usuarios desde administracion permitiendo seleccionar el rol.
    public static function crearAdmin($idRol, $nombre, $apellidos, $correo, $telefono, $fotoUrl, $passwordHash, $estado)
    {
        $conexion = Database::obtenerConexion();

        $sql = "INSERT INTO usuarios (id_rol, nombre, apellidos, correo, telefono, foto_url, password_hash, estado)
                VALUES (:id_rol, :nombre, :apellidos, :correo, :telefono, :foto_url, :password_hash, :estado)";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_rol', $idRol);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellidos', $apellidos);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':foto_url', $fotoUrl);
        $stmt->bindParam(':password_hash', $passwordHash);
        $stmt->bindParam(':estado', $estado);
        $stmt->execute();

        return $conexion->lastInsertId();
    }

    // Actualiza datos generales del usuario sin tocar la contrasena.
    public static function actualizarAdmin($idUsuario, $idRol, $nombre, $apellidos, $correo, $telefono, $fotoUrl, $estado)
    {
        $conexion = Database::obtenerConexion();

        $sql = "UPDATE usuarios
                SET id_rol = :id_rol,
                    nombre = :nombre,
                    apellidos = :apellidos,
                    correo = :correo,
                    telefono = :telefono,
                    foto_url = :foto_url,
                    estado = :estado
                WHERE id_usuario = :id_usuario";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_rol', $idRol);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellidos', $apellidos);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':foto_url', $fotoUrl);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':id_usuario', $idUsuario);
        $stmt->execute();
    }

    // Cambia el estado para activar, inactivar o bloquear cuentas.
    public static function actualizarEstado($idUsuario, $estado)
    {
        $conexion = Database::obtenerConexion();

        $sql = "UPDATE usuarios SET estado = :estado WHERE id_usuario = :id_usuario";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':id_usuario', $idUsuario);
        $stmt->execute();
    }

    public static function actualizarPassword($idUsuario, $passwordHash)
    {
        $conexion = Database::obtenerConexion();

        $sql = "UPDATE usuarios SET password_hash = :password_hash WHERE id_usuario = :id";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':password_hash', $passwordHash);
        $stmt->bindParam(':id', $idUsuario);
        $stmt->execute();
    }
}
