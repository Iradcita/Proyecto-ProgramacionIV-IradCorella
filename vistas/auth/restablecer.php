<?php $tituloPagina = 'Restablecer contraseña - NubeTurismo'; ?>
<?php require BASE_PATH . '/vistas/layouts/header.php'; ?>

<section class="formulario-contenedor">
    <h1>Restablecer contraseña</h1>

    <?php require BASE_PATH . '/vistas/layouts/mensajes.php'; ?>

    <form method="post" action="<?php echo BASE_URL; ?>/restablecer.php" novalidate>
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token'] ?? '', ENT_QUOTES); ?>">

        <div class="campo">
            <label for="password">Nueva contraseña</label>
            <input type="password" id="password" name="password" minlength="8" required autofocus>
            <small>Mínimo 8 caracteres, con letras y números.</small>
        </div>

        <div class="campo">
            <label for="password_confirmacion">Confirmar nueva contraseña</label>
            <input type="password" id="password_confirmacion" name="password_confirmacion" minlength="8" required>
        </div>

        <button type="submit" class="boton boton--primario">Actualizar contraseña</button>
    </form>
</section>

<?php require BASE_PATH . '/vistas/layouts/footer.php'; ?>
