<?php require BASE_PATH . '/vistas/layouts/header.php'; ?>

<?php
// Valores reutilizados para que el mismo formulario sirva para crear y editar.
$editando = !empty($destino);
$accionFormulario = BASE_URL . '/admin_destinos.php?accion=guardar';
?>

<!-- Formulario de mantenimiento de destinos. -->
<section class="formulario-admin">
    <div class="admin-encabezado">
        <div>
            <h1><?php echo $editando ? 'Editar destino' : 'Nuevo destino'; ?></h1>
            <p>Completa la informacion general, imagen y ubicacion.</p>
        </div>
        <a class="boton boton--compacto" href="<?php echo BASE_URL; ?>/admin_destinos.php">Volver</a>
    </div>

    <?php require BASE_PATH . '/vistas/layouts/mensajes.php'; ?>

    <form method="post" action="<?php echo $accionFormulario; ?>" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">
        <input type="hidden" name="id_destino" value="<?php echo $editando ? (int) $destino['id_destino'] : 0; ?>">

        <div class="grid-formulario">
            <div class="campo">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" maxlength="255" required value="<?php echo htmlspecialchars($destino['nombre'] ?? '', ENT_QUOTES); ?>">
            </div>
            <div class="campo">
                <label for="id_provincia">Provincia</label>
                <select id="id_provincia" name="id_provincia" required>
                    <option value="">Seleccione</option>
                    <?php foreach ($provincias as $provincia): ?>
                        <option value="<?php echo (int) $provincia['id_provincia']; ?>" <?php echo $editando && (int) $destino['id_provincia'] === (int) $provincia['id_provincia'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($provincia['nombre'], ENT_QUOTES); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo campo--ancho">
                <label for="descripcion">Descripcion</label>
                <textarea id="descripcion" name="descripcion" maxlength="1000" required><?php echo htmlspecialchars($destino['descripcion'] ?? '', ENT_QUOTES); ?></textarea>
            </div>
            <div class="campo campo--ancho">
                <label for="imagen_archivo">Imagen principal</label>

                <?php if (!empty($destino['imagen_principal'])): ?>
                    <!-- Vista previa de la imagen que ya tiene guardada -->
                    <div class="imagen-actual">
                        <img src="<?php echo BASE_URL . '/' . htmlspecialchars($destino['imagen_principal'], ENT_QUOTES); ?>"
                             alt="Imagen actual"
                             onerror="this.style.display='none';">
                        <label class="check-linea">
                            <input type="checkbox" name="quitar_imagen" value="1">
                            Quitar la imagen actual
                        </label>
                    </div>
                <?php endif; ?>

                <input type="file" id="imagen_archivo" name="imagen_archivo" accept="image/jpeg,image/png,image/webp">
                <small>Formatos permitidos: JPG, PNG o WEBP. Peso maximo: 2 MB.
                       Si no escoges ninguna, se conserva la que ya estaba.</small>

                <!-- Se manda la ruta actual escondida para no perderla al guardar -->
                <input type="hidden" name="imagen_actual" value="<?php echo htmlspecialchars($destino['imagen_principal'] ?? '', ENT_QUOTES); ?>">
            </div>
            <div class="campo">
                <label for="estado">Estado</label>
                <select id="estado" name="estado">
                    <option value="1" <?php echo !$editando || (int) $destino['estado'] === 1 ? 'selected' : ''; ?>>Activo</option>
                    <option value="0" <?php echo $editando && (int) $destino['estado'] === 0 ? 'selected' : ''; ?>>Inactivo</option>
                </select>
            </div>
            <div class="campo">
                <label for="latitud">Latitud</label>
                <input type="number" step="0.0000001" id="latitud" name="latitud" value="<?php echo htmlspecialchars($destino['latitud'] ?? '', ENT_QUOTES); ?>">
            </div>
            <div class="campo">
                <label for="longitud">Longitud</label>
                <input type="number" step="0.0000001" id="longitud" name="longitud" value="<?php echo htmlspecialchars($destino['longitud'] ?? '', ENT_QUOTES); ?>">
            </div>
        </div>

        <button class="boton boton--primario" type="submit">Guardar destino</button>
    </form>
</section>

<?php require BASE_PATH . '/vistas/layouts/footer.php'; ?>
