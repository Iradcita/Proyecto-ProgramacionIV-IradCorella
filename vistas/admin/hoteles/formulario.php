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

    <form method="post" action="<?php echo BASE_URL; ?>/admin_hoteles.php?accion=guardar" enctype="multipart/form-data">
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
                <input type="tel" id="telefono" name="telefono" maxlength="25" pattern="(\+506[ -]?)?[245678][0-9]{3}[ -]?[0-9]{4}" title="Debe tener 8 digitos y empezar con 2, 4, 5, 6, 7 u 8. Ejemplo: 8888-7777" placeholder="2222-3333" value="<?php echo htmlspecialchars($hotel['telefono'] ?? '', ENT_QUOTES); ?>">
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
                <label for="imagen_archivo">Imagen</label>

                <?php if (!empty($hotel['imagen'])): ?>
                    <!-- Vista previa de la imagen que ya tiene guardada -->
                    <div class="imagen-actual">
                        <img src="<?php echo BASE_URL . '/' . htmlspecialchars($hotel['imagen'], ENT_QUOTES); ?>"
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
                <input type="hidden" name="imagen_actual" value="<?php echo htmlspecialchars($hotel['imagen'] ?? '', ENT_QUOTES); ?>">
            </div>
        </div>

        <button class="boton boton--primario" type="submit">Guardar hotel</button>
    </form>
</section>

<?php require BASE_PATH . '/vistas/layouts/footer.php'; ?>
