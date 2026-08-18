<?php require BASE_PATH . '/vistas/layouts/header.php'; ?>

<?php
// Ruta absoluta web de la fotografia actual si existe.
$fotoPerfil = !empty($usuario['foto_url']) ? BASE_URL . '/' . $usuario['foto_url'] : '';
?>

<!-- Panel de perfil del usuario conectado. -->
<section class="admin-encabezado">
    <div>
        <h1>Mi perfil</h1>
        <p>Actualiza tus datos personales, fotografia y contrasena.</p>
    </div>
    <a class="boton boton--compacto" href="<?php echo BASE_URL; ?>/mis_reservaciones.php">Mis reservaciones</a>
</section>

<?php require BASE_PATH . '/vistas/layouts/mensajes.php'; ?>

<div class="perfil-grid">
    <!-- Resumen visual de la cuenta. -->
    <aside class="perfil-resumen">
        <div class="perfil-avatar">
            <?php if ($fotoPerfil !== ''): ?>
                <img onerror="this.style.display='none';" src="<?php echo htmlspecialchars($fotoPerfil, ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($usuario['nombre'], ENT_QUOTES); ?>">
            <?php else: ?>
                <span><?php echo htmlspecialchars(substr($usuario['nombre'], 0, 1), ENT_QUOTES); ?></span>
            <?php endif; ?>
        </div>
        <h2><?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellidos'], ENT_QUOTES); ?></h2>
        <p><?php echo htmlspecialchars($usuario['correo'], ENT_QUOTES); ?></p>
        <span class="estado estado--activo"><?php echo htmlspecialchars($usuario['rol_nombre'], ENT_QUOTES); ?></span>
    </aside>

    <section class="perfil-paneles">
        <!-- Formulario para datos personales. -->
        <form class="formulario-admin" method="post" action="<?php echo BASE_URL; ?>/perfil.php">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">
            <input type="hidden" name="accion" value="datos">

            <h2>Datos personales</h2>
            <div class="grid-formulario">
                <div class="campo">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" maxlength="100" required value="<?php echo htmlspecialchars($usuario['nombre'], ENT_QUOTES); ?>">
                </div>
                <div class="campo">
                    <label for="apellidos">Apellidos</label>
                    <input type="text" id="apellidos" name="apellidos" maxlength="150" required value="<?php echo htmlspecialchars($usuario['apellidos'], ENT_QUOTES); ?>">
                </div>
                <div class="campo">
                    <label for="correo">Correo</label>
                    <input type="email" id="correo" name="correo" maxlength="190" required value="<?php echo htmlspecialchars($usuario['correo'], ENT_QUOTES); ?>">
                </div>
                <div class="campo">
                    <label for="telefono">Telefono</label>
                    <input type="text" id="telefono" name="telefono" maxlength="25" value="<?php echo htmlspecialchars($usuario['telefono'] ?? '', ENT_QUOTES); ?>">
                </div>
            </div>
            <button class="boton boton--primario" type="submit">Guardar datos</button>
        </form>

        <!-- Formulario para subir o eliminar fotografia. -->
        <form class="formulario-admin" method="post" action="<?php echo BASE_URL; ?>/perfil.php" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">
            <input type="hidden" name="accion" value="foto">

            <h2>Fotografia</h2>
            <div class="campo">
                <label for="foto">Nueva fotografia</label>
                <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp">
                <small>Formatos permitidos: JPG, PNG o WEBP. Maximo 2 MB.</small>
            </div>
            <?php if ($fotoPerfil !== ''): ?>
                <label class="check-linea">
                    <input type="checkbox" name="eliminar_foto" value="1">
                    Eliminar fotografia actual
                </label>
            <?php endif; ?>
            <button class="boton boton--primario" type="submit">Guardar fotografia</button>
        </form>

        <!-- Formulario para cambiar contrasena desde sesion iniciada. -->
        <form class="formulario-admin" method="post" action="<?php echo BASE_URL; ?>/perfil.php">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">
            <input type="hidden" name="accion" value="password">

            <h2>Cambiar contrasena</h2>
            <div class="grid-formulario">
                <div class="campo">
                    <label for="password_actual">Contrasena actual</label>
                    <input type="password" id="password_actual" name="password_actual" required>
                </div>
                <div class="campo">
                    <label for="password_nueva">Nueva contrasena</label>
                    <input type="password" id="password_nueva" name="password_nueva" minlength="8" required>
                </div>
                <div class="campo campo--ancho">
                    <label for="password_confirmacion">Confirmar nueva contrasena</label>
                    <input type="password" id="password_confirmacion" name="password_confirmacion" minlength="8" required>
                    <small>Minimo 8 caracteres, con letras y numeros.</small>
                </div>
            </div>
            <button class="boton boton--primario" type="submit">Actualizar contrasena</button>
        </form>
    </section>
</div>

<?php require BASE_PATH . '/vistas/layouts/footer.php'; ?>
