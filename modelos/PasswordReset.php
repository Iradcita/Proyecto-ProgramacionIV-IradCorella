<?php

// Modelo para los tokens de restablecimiento de contrasena (tabla password_resets)
class PasswordReset
{
    public static function crear($idUsuario, $tokenHash, $expiraEn)
    {
        $conexion = Database::obtenerConexion();

        $sql = "INSERT INTO password_resets (id_usuario, token_hash, expira_en, usado)
                VALUES (:id_usuario, :token_hash, :expira_en, 0)";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_usuario', $idUsuario);
        $stmt->bindParam(':token_hash', $tokenHash);
        $stmt->bindParam(':expira_en', $expiraEn);
        $stmt->execute();
    }

    public static function buscarValido($tokenHash)
    {
        $conexion = Database::obtenerConexion();

        $sql = "SELECT * FROM password_resets
                WHERE token_hash = :token_hash AND usado = 0 AND expira_en > NOW()";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':token_hash', $tokenHash);
        $stmt->execute();

        $fila = $stmt->fetch();

        return $fila ? $fila : null;
    }

    public static function marcarUsado($idReset)
    {
        $conexion = Database::obtenerConexion();

        $sql = "UPDATE password_resets SET usado = 1 WHERE id_reset = :id";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id', $idReset);
        $stmt->execute();
    }

    public static function invalidarDeUsuario($idUsuario)
    {
        $conexion = Database::obtenerConexion();

        $sql = "UPDATE password_resets SET usado = 1 WHERE id_usuario = :id_usuario AND usado = 0";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_usuario', $idUsuario);
        $stmt->execute();
    }
}
