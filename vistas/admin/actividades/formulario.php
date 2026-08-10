<?php require BASE_PATH . '/vistas/layouts/header.php'; ?>

<?php
// Variable que permite reutilizar este formulario para alta y edicion.
$editando = !empty($actividad);
?>

<!-- Formulario administrativo de actividades. -->
<section class="formulario-admin">
    <div class="admin-encabezado">
        <div>
            <h1><?php echo $editando ? 'Editar actividad' : 'Nueva actividad'; ?></h1>
            <p>Define la experiencia turistica, precio, duracion y cupo.</p>
        </div>
        <a class="boton boton--compacto" href="<?php echo BASE_URL; ?>/admin_actividades.php">Volver</a>
    </div>

    <?php require BASE_PATH . '/vistas/layouts/mensajes.php'; ?>

    <form method="post" action="<?php echo BASE_URL; ?>/admin_actividades.php?accion=guardar">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">
        <input type="hidden" name="id_actividad" value="<?php echo $editando ? (int) $actividad['id_actividad'] : 0; ?>">

        <div class="grid-formulario">
            <div class="campo">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" maxlength="150" required value="<?php echo htmlspecialchars($actividad['nombre'] ?? '', ENT_QUOTES); ?>">
            </div>
            <div class="campo">
                <label for="id_destino">Destino</label>
                <select id="id_destino" name="id_destino" required>
                    <option value="">Seleccione</option>
                    <?php foreach ($destinos as $destino): ?>
                        <option value="<?php echo (int) $destino['id_destino']; ?>" <?php echo $editando && (int) $actividad['id_destino'] === (int) $destino['id_destino'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($destino['nombre'], ENT_QUOTES); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo">
                <label for="precio">Precio</label>
                <input type="number" step="0.01" id="precio" name="precio" min="1" required value="<?php echo htmlspecialchars($actividad['precio'] ?? '', ENT_QUOTES); ?>">
            </div>
            <div class="campo">
                <label for="duracion_minutos">Duracion en minutos</label>
                <input type="number" id="duracion_minutos" name="duracion_minutos" min="1" required value="<?php echo htmlspecialchars($actividad['duracion_minutos'] ?? '', ENT_QUOTES); ?>">
            </div>
            <div class="campo">
                <label for="cupo_maximo">Cupo maximo</label>
                <input type="number" id="cupo_maximo" name="cupo_maximo" min="1" required value="<?php echo htmlspecialchars($actividad['cupo_maximo'] ?? '', ENT_QUOTES); ?>">
            </div>
            <div class="campo">
                <label for="estado">Estado</label>
                <select id="estado" name="estado">
                    <option value="1" <?php echo !$editando || (int) $actividad['estado'] === 1 ? 'selected' : ''; ?>>Activo</option>
                    <option value="0" <?php echo $editando && (int) $actividad['estado'] === 0 ? 'selected' : ''; ?>>Inactivo</option>
                </select>
            </div>
            <div class="campo campo--ancho">
                <label for="descripcion">Descripcion</label>
                <textarea id="descripcion" name="descripcion" maxlength="1000" required><?php echo htmlspecialchars($actividad['descripcion'] ?? '', ENT_QUOTES); ?></textarea>
            </div>
            <div class="campo campo--ancho">
                <label for="imagen">Imagen</label>
                <input type="text" id="imagen" name="imagen" maxlength="500" value="<?php echo htmlspecialchars($actividad['imagen'] ?? '', ENT_QUOTES); ?>">
            </div>
        </div>

        <button class="boton boton--primario" type="submit">Guardar actividad</button>
    </form>
</section>

<?php require BASE_PATH . '/vistas/layouts/footer.php'; ?>
