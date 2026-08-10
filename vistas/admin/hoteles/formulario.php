<?php require BASE_PATH . '/vistas/layouts/header.php'; ?>

<?php
// El mismo formulario funciona para crear y actualizar hoteles.
$editando = !empty($hotel);
?>

<!-- Formulario administrativo de hoteles. -->
<section class="formulario-admin">
    <div class="admin-encabezado">
        <div>
            <h1><?php echo $editando ? 'Editar hotel' : 'Nuevo hotel'; ?></h1>
            <p>Registra la relacion con destino, tarifas y disponibilidad.</p>
        </div>
        <a class="boton boton--compacto" href="<?php echo BASE_URL; ?>/admin_hoteles.php">Volver</a>
    </div>

    <?php require BASE_PATH . '/vistas/layouts/mensajes.php'; ?>

    <form method="post" action="<?php echo BASE_URL; ?>/admin_hoteles.php?accion=guardar">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">
        <input type="hidden" name="id_hotel" value="<?php echo $editando ? (int) $hotel['id_hotel'] : 0; ?>">

        <div class="grid-formulario">
            <div class="campo">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" maxlength="255" required value="<?php echo htmlspecialchars($hotel['nombre'] ?? '', ENT_QUOTES); ?>">
            </div>
            <div class="campo">
                <label for="id_destino">Destino</label>
                <select id="id_destino" name="id_destino" required>
                    <option value="">Seleccione</option>
                    <?php foreach ($destinos as $destino): ?>
                        <option value="<?php echo (int) $destino['id_destino']; ?>" <?php echo $editando && (int) $hotel['id_destino'] === (int) $destino['id_destino'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($destino['nombre'], ENT_QUOTES); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo">
                <label for="categoria">Categoria</label>
                <input type="number" id="categoria" name="categoria" min="1" max="5" required value="<?php echo htmlspecialchars($hotel['categoria'] ?? '', ENT_QUOTES); ?>">
            </div>
            <div class="campo">
                <label for="precio_noche">Precio por noche</label>
                <input type="number" step="0.01" id="precio_noche" name="precio_noche" min="1" required value="<?php echo htmlspecialchars($hotel['precio_noche'] ?? '', ENT_QUOTES); ?>">
            </div>
            <div class="campo">
                <label for="cantidad_habitaciones">Habitaciones</label>
                <input type="number" id="cantidad_habitaciones" name="cantidad_habitaciones" min="1" required value="<?php echo htmlspecialchars($hotel['cantidad_habitaciones'] ?? '', ENT_QUOTES); ?>">
            </div>
            <div class="campo">
                <label for="telefono">Telefono</label>
                <input type="text" id="telefono" name="telefono" maxlength="25" value="<?php echo htmlspecialchars($hotel['telefono'] ?? '', ENT_QUOTES); ?>">
            </div>
            <div class="campo">
                <label for="correo">Correo</label>
                <input type="email" id="correo" name="correo" maxlength="190" value="<?php echo htmlspecialchars($hotel['correo'] ?? '', ENT_QUOTES); ?>">
            </div>
            <div class="campo">
                <label for="estado">Estado</label>
                <select id="estado" name="estado">
                    <option value="1" <?php echo !$editando || (int) $hotel['estado'] === 1 ? 'selected' : ''; ?>>Activo</option>
                    <option value="0" <?php echo $editando && (int) $hotel['estado'] === 0 ? 'selected' : ''; ?>>Inactivo</option>
                </select>
            </div>
            <div class="campo campo--ancho">
                <label for="direccion">Direccion</label>
                <input type="text" id="direccion" name="direccion" maxlength="255" required value="<?php echo htmlspecialchars($hotel['direccion'] ?? '', ENT_QUOTES); ?>">
            </div>
            <div class="campo campo--ancho">
                <label for="descripcion">Descripcion</label>
                <textarea id="descripcion" name="descripcion" maxlength="1000" required><?php echo htmlspecialchars($hotel['descripcion'] ?? '', ENT_QUOTES); ?></textarea>
            </div>
            <div class="campo campo--ancho">
                <label for="imagen">Imagen</label>
                <input type="text" id="imagen" name="imagen" maxlength="500" value="<?php echo htmlspecialchars($hotel['imagen'] ?? '', ENT_QUOTES); ?>">
            </div>
        </div>

        <button class="boton boton--primario" type="submit">Guardar hotel</button>
    </form>
</section>

<?php require BASE_PATH . '/vistas/layouts/footer.php'; ?>
