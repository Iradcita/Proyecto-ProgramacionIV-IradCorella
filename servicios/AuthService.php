<?php

// Aqui va la logica de negocio del login, registro y recuperacion de contraseña.
class AuthService
{
    public function login($correo, $password)
    {
        $usuario = Usuario::buscarPorCorreo($correo);

        if (!$usuario || !password_verify($password, $usuario['password_hash'])) {
            throw new Exception('Correo o contraseña incorrectos.');
        }

        if ($usuario['estado'] === 'bloqueado') {
            throw new Exception('Tu cuenta está bloqueada. Contacta al administrador.');
        }

        if ($usuario['estado'] === 'inactivo') {
            throw new Exception('Tu cuenta está inactiva. Contacta al administrador.');
        }

        return $usuario;
    }

    public function registrar($nombre, $apellidos, $correo, $telefono, $password, $confirmacion)
    {
        $nombre = trim($nombre);
        $apellidos = trim($apellidos);
        $correo = trim($correo);
        $telefono = trim($telefono);

        if ($nombre === '' || $apellidos === '') {
            throw new Exception('El nombre y los apellidos son obligatorios.');
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('El correo electrónico no es válido.');
        }

        if ($password !== $confirmacion) {
            throw new Exception('Las contraseñas no coinciden.');
        }

        if (!$this->passwordEsValida($password)) {
            throw new Exception('La contraseña debe tener al menos 8 caracteres, con letras y números.');
        }

        if (Usuario::existeCorreo($correo)) {
            throw new Exception('Ese correo ya está registrado.');
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        return Usuario::crear($nombre, $apellidos, $correo, $telefono, $passwordHash);
    }

    public function solicitarRestablecimiento($correo)
    {
        $correo = trim($correo);

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        $usuario = Usuario::buscarPorCorreo($correo);

        if (!$usuario) {
            return null;
        }

        // Token de restablecimiento simulado: no se envia correo real,
        // se genera el enlace y se muestra directo en pantalla.
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expiraEn = date('Y-m-d H:i:s', time() + 1800);

        PasswordReset::invalidarDeUsuario($usuario['id_usuario']);
        PasswordReset::crear($usuario['id_usuario'], $tokenHash, $expiraEn);

        return $token;
    }

    public function restablecerPassword($token, $password, $confirmacion)
    {
        if ($password !== $confirmacion) {
            throw new Exception('Las contraseñas no coinciden.');
        }

        if (!$this->passwordEsValida($password)) {
            throw new Exception('La contraseña debe tener al menos 8 caracteres, con letras y números.');
        }

        $tokenHash = hash('sha256', $token);
        $registro = PasswordReset::buscarValido($tokenHash);

        if (!$registro) {
            throw new Exception('El enlace de restablecimiento no es válido o ha expirado.');
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        Usuario::actualizarPassword($registro['id_usuario'], $passwordHash);
        PasswordReset::marcarUsado($registro['id_reset']);
    }

    private function passwordEsValida($password)
    {
        return strlen($password) >= 8
            && preg_match('/[A-Za-z]/', $password)
            && preg_match('/[0-9]/', $password);
    }
}
