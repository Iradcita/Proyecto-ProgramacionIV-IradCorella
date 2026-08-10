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

    <form method="post" action="<?php echo $accionFormulario; ?>">
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
            <div class="campo">
                <label for="imagen_principal">Imagen principal</label>
                <input type="text" id="imagen_principal" name="imagen_principal" maxlength="500" value="<?php echo htmlspecialchars($destino['imagen_principal'] ?? '', ENT_QUOTES); ?>">
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
