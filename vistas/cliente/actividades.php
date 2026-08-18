<?php require BASE_PATH . '/vistas/layouts/header.php'; ?>

<!-- Busqueda de actividades para clientes. -->
<section class="admin-encabezado">
    <div>
        <h1>Actividades</h1>
        <p>Encuentra experiencias para sumar a tu viaje.</p>
    </div>
    <a class="boton boton--compacto boton--primario" href="<?php echo BASE_URL; ?>/reservar.php">Crear reserva</a>
</section>

<?php require BASE_PATH . '/vistas/layouts/mensajes.php'; ?>

<!-- Filtros del catalogo de actividades. -->
<form class="filtros" method="get" action="<?php echo BASE_URL; ?>/actividades.php">
    <input type="text" name="busqueda" placeholder="Buscar actividad o destino" value="<?php echo htmlspecialchars($busqueda, ENT_QUOTES); ?>">
    <select name="destino">
        <option value="0">Todos los destinos</option>
        <?php foreach ($destinos as $destino): ?>
            <option value="<?php echo (int) $destino['id_destino']; ?>" <?php echo (int) $idDestino === (int) $destino['id_destino'] ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($destino['nombre'], ENT_QUOTES); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button class="boton boton--compacto" type="submit">Buscar</button>
</form>

<!-- Resultados de actividades disponibles. -->
<div class="catalogo-grid">
    <?php foreach ($actividades as $actividad): ?>
        <article class="catalogo-item">
            <div class="catalogo-item__media">
                <?php if (!empty($actividad['imagen'])): ?>
                    <img onerror="this.style.display='none';" src="<?php echo htmlspecialchars($actividad['imagen'], ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($actividad['nombre'], ENT_QUOTES); ?>">
                <?php else: ?>
                    <span><?php echo htmlspecialchars(substr($actividad['nombre'], 0, 1), ENT_QUOTES); ?></span>
                <?php endif; ?>
            </div>
            <div class="catalogo-item__contenido">
                <h2><?php echo htmlspecialchars($actividad['nombre'], ENT_QUOTES); ?></h2>
                <p class="texto-muted"><?php echo htmlspecialchars($actividad['destino_nombre'], ENT_QUOTES); ?></p>
                <p><?php echo htmlspecialchars($actividad['descripcion'], ENT_QUOTES); ?></p>
                <p><strong>CRC <?php echo number_format((float) $actividad['precio'], 2); ?></strong> por persona</p>
                <p class="texto-muted"><?php echo (int) $actividad['duracion_minutos']; ?> min · cupo <?php echo (int) $actividad['cupo_maximo']; ?></p>
                <div class="catalogo-item__acciones">
                    <a href="<?php echo BASE_URL; ?>/reservar.php?destino=<?php echo (int) $actividad['id_destino']; ?>">Reservar viaje</a>
                </div>
            </div>
        </article>
    <?php endforeach; ?>
    <?php if (empty($actividades)): ?>
        <p>No hay actividades que coincidan con la busqueda.</p>
    <?php endif; ?>
</div>

<?php require BASE_PATH . '/vistas/layouts/footer.php'; ?>
