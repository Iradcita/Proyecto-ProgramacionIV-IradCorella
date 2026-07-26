<?php $tituloPagina = 'Recuperar contraseña - NubeTurismo'; ?>
<?php require BASE_PATH . '/vistas/layouts/header.php'; ?>

<section class="formulario-contenedor">
    <h1>Recuperar contraseña</h1>
    <p>Ingresa tu correo y generaremos un enlace de restablecimiento.</p>

    <?php require BASE_PATH . '/vistas/layouts/mensajes.php'; ?>

    <?php if (!empty($enlaceRestablecimiento)): ?>
        <p class="enlace-simulado">
            Enlace simulado (válido 30 min):
            <a href="<?php echo htmlspecialchars($enlaceRestablecimiento, ENT_QUOTES); ?>">
                <?php echo htmlspecialchars($enlaceRestablecimiento, ENT_QUOTES); ?>
            </a>
        </p>
    <?php endif; ?>

    <form method="post" action="<?php echo BASE_URL; ?>/recuperar.php" novalidate>
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">

        <div class="campo">
            <label for="correo">Correo electrónico</label>
            <input type="email" id="correo" name="correo" maxlength="190" required autofocus>
        </div>

        <button type="submit" class="boton boton--primario">Generar enlace</button>
    </form>

    <p class="enlace-secundario"><a href="<?php echo BASE_URL; ?>/login.php">Volver a iniciar sesión</a></p>
</section>

<?php require BASE_PATH . '/vistas/layouts/footer.php'; ?>
