<?php

// Modelo de usuarios: guarda los datos y tambien las consultas a la tabla,
// para no tener que crear una clase aparte solo para eso.
class Usuario
{
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
