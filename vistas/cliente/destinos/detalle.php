<?php require BASE_PATH . '/vistas/layouts/header.php'; ?>

<!-- Detalle del destino seleccionado por el cliente. -->
<section class="detalle-destino">
    <div>
        <p class="texto-muted"><?php echo htmlspecialchars($destino['provincia_nombre'], ENT_QUOTES); ?></p>
        <h1><?php echo htmlspecialchars($destino['nombre'], ENT_QUOTES); ?></h1>
        <p><?php echo htmlspecialchars($destino['descripcion'], ENT_QUOTES); ?></p>
        <?php if (!empty($destino['latitud']) && !empty($destino['longitud'])): ?>
            <p class="texto-muted">Ubicacion: <?php echo htmlspecialchars($destino['latitud'] . ', ' . $destino['longitud'], ENT_QUOTES); ?></p>
        <?php endif; ?>
        <a class="boton boton--compacto boton--primario" href="<?php echo BASE_URL; ?>/reservar.php?destino=<?php echo (int) $destino['id_destino']; ?>">Reservar en este destino</a>
    </div>
    <div class="detalle-destino__imagen">
        <?php if (!empty($destino['imagen_principal'])): ?>
            <img src="<?php echo htmlspecialchars($destino['imagen_principal'], ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($destino['nombre'], ENT_QUOTES); ?>">
        <?php else: ?>
            <span><?php echo htmlspecialchars(substr($destino['nombre'], 0, 1), ENT_QUOTES); ?></span>
        <?php endif; ?>
    </div>
</section>

<?php require BASE_PATH . '/vistas/layouts/mensajes.php'; ?>

<!-- Hoteles activos relacionados con el destino. -->
<section class="seccion-cliente">
    <div class="admin-encabezado">
        <div>
            <h2>Hoteles disponibles</h2>
            <p>Opciones de hospedaje para este destino.</p>
        </div>
    </div>
    <div class="catalogo-grid catalogo-grid--compacto">
        <?php foreach ($hoteles as $hotel): ?>
            <article class="catalogo-item">
                <div class="catalogo-item__contenido">
                    <h3><?php echo htmlspecialchars($hotel['nombre'], ENT_QUOTES); ?></h3>
                    <p><?php echo htmlspecialchars($hotel['descripcion'], ENT_QUOTES); ?></p>
                    <p><strong>CRC <?php echo number_format((float) $hotel['precio_noche'], 2); ?></strong> por noche</p>
                    <a href="<?php echo BASE_URL; ?>/reservar.php?destino=<?php echo (int) $destino['id_destino']; ?>&hotel=<?php echo (int) $hotel['id_hotel']; ?>">Reservar hotel</a>
                </div>
            </article>
        <?php endforeach; ?>
        <?php if (empty($hoteles)): ?>
            <p>No hay hoteles disponibles para este destino.</p>
        <?php endif; ?>
    </div>
</section>

<!-- Actividades activas relacionadas con el destino. -->
<section class="seccion-cliente">
    <div class="admin-encabezado">
        <div>
            <h2>Actividades disponibles</h2>
            <p>Experiencias que puedes sumar a tu reserva.</p>
        </div>
    </div>
    <div class="catalogo-grid catalogo-grid--compacto">
        <?php foreach ($actividades as $actividad): ?>
            <article class="catalogo-item">
                <div class="catalogo-item__contenido">
                    <h3><?php echo htmlspecialchars($actividad['nombre'], ENT_QUOTES); ?></h3>
                    <p><?php echo htmlspecialchars($actividad['descripcion'], ENT_QUOTES); ?></p>
                    <p><strong>CRC <?php echo number_format((float) $actividad['precio'], 2); ?></strong> por persona</p>
                    <p class="texto-muted"><?php echo (int) $actividad['duracion_minutos']; ?> min, cupo <?php echo (int) $actividad['cupo_maximo']; ?></p>
                </div>
            </article>
        <?php endforeach; ?>
        <?php if (empty($actividades)): ?>
            <p>No hay actividades disponibles para este destino.</p>
        <?php endif; ?>
    </div>
</section>

<!-- Resenas aprobadas y formulario del cliente. -->
<section class="seccion-cliente">
    <div class="admin-encabezado">
        <div>
            <h2>Resenas del destino</h2>
            <p>Comentarios aprobados por administracion.</p>
        </div>
    </div>

    <div class="catalogo-grid catalogo-grid--compacto">
        <?php foreach ($resenas as $resena): ?>
            <article class="detalle-bloque">
                <h3><?php echo (int) $resena['calificacion']; ?>/5</h3>
                <p><?php echo htmlspecialchars($resena['comentario'] ?? '', ENT_QUOTES); ?></p>
                <p class="texto-muted"><?php echo htmlspecialchars($resena['usuario_nombre'], ENT_QUOTES); ?></p>
            </article>
        <?php endforeach; ?>
        <?php if (empty($resenas)): ?>
            <p>No hay resenas aprobadas para este destino.</p>
        <?php endif; ?>
    </div>

    <form class="formulario-admin seccion-cliente" method="post" action="<?php echo BASE_URL; ?>/resenas.php">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">
        <input type="hidden" name="id_destino" value="<?php echo (int) $destino['id_destino']; ?>">

        <h2><?php echo $resenaUsuario ? 'Actualizar mi resena' : 'Agregar mi resena'; ?></h2>
        <div class="grid-formulario">
            <div class="campo">
                <label for="calificacion">Calificacion</label>
                <select id="calificacion" name="calificacion" required>
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <option value="<?php echo $i; ?>" <?php echo $resenaUsuario && (int) $resenaUsuario['calificacion'] === $i ? 'selected' : ''; ?>><?php echo $i; ?>/5</option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="campo campo--ancho">
                <label for="comentario">Comentario</label>
                <textarea id="comentario" name="comentario" maxlength="1000"><?php echo htmlspecialchars($resenaUsuario['comentario'] ?? '', ENT_QUOTES); ?></textarea>
                <small>La resena queda pendiente de revision antes de mostrarse.</small>
            </div>
        </div>
        <button class="boton boton--primario" type="submit">Enviar resena</button>
    </form>
</section>

<?php require BASE_PATH . '/vistas/layouts/footer.php'; ?>
