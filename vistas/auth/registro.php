<?php $tituloPagina = 'Crear cuenta - NubeTurismo'; ?>
<?php require BASE_PATH . '/vistas/layouts/header.php'; ?>

<section class="formulario-contenedor">
    <h1>Crear cuenta</h1>

    <?php require BASE_PATH . '/vistas/layouts/mensajes.php'; ?>

    <form method="post" action="<?php echo BASE_URL; ?>/registro.php" novalidate>
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">

        <div class="campo">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" maxlength="100" required autofocus
                   value="<?php echo htmlspecialchars($valores['nombre'] ?? '', ENT_QUOTES); ?>">
        </div>

        <div class="campo">
            <label for="apellidos">Apellidos</label>
            <input type="text" id="apellidos" name="apellidos" maxlength="150" required
                   value="<?php echo htmlspecialchars($valores['apellidos'] ?? '', ENT_QUOTES); ?>">
        </div>

        <div class="campo">
            <label for="correo">Correo electrónico</label>
            <input type="email" id="correo" name="correo" maxlength="190" required
                   value="<?php echo htmlspecialchars($valores['correo'] ?? '', ENT_QUOTES); ?>">
        </div>

        <div class="campo">
            <label for="telefono">Teléfono (opcional)</label>
            <input type="tel" id="telefono" name="telefono" maxlength="25"
                   value="<?php echo htmlspecialchars($valores['telefono'] ?? '', ENT_QUOTES); ?>">
        </div>

        <div class="campo">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" minlength="8" required>
            <small>Mínimo 8 caracteres, con letras y números.</small>
        </div>

        <div class="campo">
            <label for="password_confirmacion">Confirmar contraseña</label>
            <input type="password" id="password_confirmacion" name="password_confirmacion" minlength="8" required>
        </div>

        <button type="submit" class="boton boton--primario">Crear cuenta</button>
    </form>

    <p class="enlace-secundario">¿Ya tienes cuenta? <a href="<?php echo BASE_URL; ?>/login.php">Inicia sesión</a></p>
</section>

<?php require BASE_PATH . '/vistas/layouts/footer.php'; ?>
