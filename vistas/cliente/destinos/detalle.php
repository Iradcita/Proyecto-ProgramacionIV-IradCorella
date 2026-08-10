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

<?php require BASE_PATH . '/vistas/layouts/footer.php'; ?>
