<?php $tituloPagina = 'Iniciar sesión - NubeTurismo'; ?>
<?php require BASE_PATH . '/vistas/layouts/header.php'; ?>

<section class="formulario-contenedor">
    <h1>Iniciar sesión</h1>

    <?php require BASE_PATH . '/vistas/layouts/mensajes.php'; ?>

    <form method="post" action="<?php echo BASE_URL; ?>/login.php" novalidate>
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">

        <div class="campo">
            <label for="correo">Correo electrónico</label>
            <input type="email" id="correo" name="correo" maxlength="190" required autofocus>
        </div>

        <div class="campo">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" minlength="8" required>
        </div>

        <button type="submit" class="boton boton--primario">Ingresar</button>
    </form>

    <p class="enlace-secundario"><a href="<?php echo BASE_URL; ?>/recuperar.php">¿Olvidaste tu contraseña?</a></p>
    <p class="enlace-secundario">¿No tienes cuenta? <a href="<?php echo BASE_URL; ?>/registro.php">Regístrate aquí</a></p>
</section>

<?php require BASE_PATH . '/vistas/layouts/footer.php'; ?>
