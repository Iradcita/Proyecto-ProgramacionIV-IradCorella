<?php require BASE_PATH . '/vistas/layouts/header.php'; ?>

<?php
// Define si el formulario actualiza un usuario existente.
$editando = !empty($usuario);
?>

<!-- Formulario de creacion y edicion de usuarios. -->
<section class="formulario-admin">
    <div class="admin-encabezado">
        <div>
            <h1><?php echo $editando ? 'Editar usuario' : 'Nuevo usuario'; ?></h1>
            <p>Gestiona datos personales, rol, estado y contrasena.</p>
        </div>
        <a class="boton boton--compacto" href="<?php echo BASE_URL; ?>/admin_usuarios.php">Volver</a>
    </div>

    <?php require BASE_PATH . '/vistas/layouts/mensajes.php'; ?>

    <form method="post" action="<?php echo BASE_URL; ?>/admin_usuarios.php?accion=guardar">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">
        <input type="hidden" name="id_usuario" value="<?php echo $editando ? (int) $usuario['id_usuario'] : 0; ?>">

        <div class="grid-formulario">
            <div class="campo">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" maxlength="100" required value="<?php echo htmlspecialchars($usuario['nombre'] ?? '', ENT_QUOTES); ?>">
            </div>
            <div class="campo">
                <label for="apellidos">Apellidos</label>
                <input type="text" id="apellidos" name="apellidos" maxlength="150" required value="<?php echo htmlspecialchars($usuario['apellidos'] ?? '', ENT_QUOTES); ?>">
            </div>
            <div class="campo">
                <label for="correo">Correo</label>
                <input type="email" id="correo" name="correo" maxlength="190" required value="<?php echo htmlspecialchars($usuario['correo'] ?? '', ENT_QUOTES); ?>">
            </div>
            <div class="campo">
                <label for="telefono">Telefono</label>
                <input type="tel" id="telefono" name="telefono" maxlength="25" pattern="(\+506[ -]?)?[245678][0-9]{3}[ -]?[0-9]{4}" title="Debe tener 8 digitos y empezar con 2, 4, 5, 6, 7 u 8. Ejemplo: 8888-7777" placeholder="8888-7777" value="<?php echo htmlspecialchars($usuario['telefono'] ?? '', ENT_QUOTES); ?>">
            </div>
            <div class="campo">
                <label for="id_rol">Rol</label>
                <select id="id_rol" name="id_rol" required>
                    <option value="">Seleccione</option>
                    <?php foreach ($roles as $rol): ?>
                        <option value="<?php echo (int) $rol['id_rol']; ?>" <?php echo $editando && (int) $usuario['id_rol'] === (int) $rol['id_rol'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($rol['nombre'], ENT_QUOTES); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo">
                <label for="estado">Estado</label>
                <select id="estado" name="estado">
                    <?php foreach (array('activo', 'inactivo', 'bloqueado') as $estado): ?>
                        <option value="<?php echo $estado; ?>" <?php echo ($usuario['estado'] ?? 'activo') === $estado ? 'selected' : ''; ?>>
                            <?php echo ucfirst($estado); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo campo--ancho">
                <label for="foto_url">Fotografia URL</label>
                <input type="text" id="foto_url" name="foto_url" maxlength="500" value="<?php echo htmlspecialchars($usuario['foto_url'] ?? '', ENT_QUOTES); ?>">
            </div>
            <div class="campo campo--ancho">
                <label for="password"><?php echo $editando ? 'Nueva contrasena (opcional)' : 'Contrasena'; ?></label>
                <input type="password" id="password" name="password" minlength="8" <?php echo $editando ? '' : 'required'; ?>>
                <small>Minimo 8 caracteres, con letras y numeros.</small>
            </div>
        </div>

        <button class="boton boton--primario" type="submit">Guardar usuario</button>
    </form>
</section>

<?php require BASE_PATH . '/vistas/layouts/footer.php'; ?>
