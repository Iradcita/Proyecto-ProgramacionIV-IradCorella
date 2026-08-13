<?php require BASE_PATH . '/vistas/layouts/header.php'; ?>

<!-- Encabezado del catalogo de destinos para clientes. -->
<section class="admin-encabezado">
    <div>
        <h1>Destinos</h1>
        <p>Explora lugares turisticos activos y elige donde reservar.</p>
    </div>
    <a class="boton boton--compacto boton--primario" href="<?php echo BASE_URL; ?>/reservar.php">Reservar</a>
</section>

<?php require BASE_PATH . '/vistas/layouts/mensajes.php'; ?>

<!-- Filtros para consultar destinos por texto y provincia. -->
<form class="filtros" method="get" action="<?php echo BASE_URL; ?>/destinos.php">
    <input type="text" name="busqueda" placeholder="Buscar destino o provincia" value="<?php echo htmlspecialchars($busqueda, ENT_QUOTES); ?>">
    <select name="provincia">
        <option value="0">Todas las provincias</option>
        <?php foreach ($provincias as $provincia): ?>
            <option value="<?php echo (int) $provincia['id_provincia']; ?>" <?php echo (int) $idProvincia === (int) $provincia['id_provincia'] ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($provincia['nombre'], ENT_QUOTES); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button class="boton boton--compacto" type="submit">Buscar</button>
</form>

<!-- Resultados del catalogo de destinos. -->
<div class="catalogo-grid">
    <?php foreach ($destinos as $destino): ?>
        <article class="catalogo-item">
            <div class="catalogo-item__media">
                <?php if (!empty($destino['imagen_principal'])): ?>
                    <img onerror="this.style.display='none';" src="<?php echo htmlspecialchars($destino['imagen_principal'], ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($destino['nombre'], ENT_QUOTES); ?>">
                <?php else: ?>
                    <span><?php echo htmlspecialchars(substr($destino['nombre'], 0, 1), ENT_QUOTES); ?></span>
                <?php endif; ?>
            </div>
            <div class="catalogo-item__contenido">
                <h2><?php echo htmlspecialchars($destino['nombre'], ENT_QUOTES); ?></h2>
                <p class="texto-muted"><?php echo htmlspecialchars($destino['provincia_nombre'], ENT_QUOTES); ?></p>
                <p><?php echo htmlspecialchars($destino['descripcion'], ENT_QUOTES); ?></p>
                <div class="catalogo-item__acciones">
                    <a href="<?php echo BASE_URL; ?>/destinos.php?accion=detalle&id=<?php echo (int) $destino['id_destino']; ?>">Ver detalle</a>
                    <a href="<?php echo BASE_URL; ?>/reservar.php?destino=<?php echo (int) $destino['id_destino']; ?>">Reservar aqui</a>
                </div>
            </div>
        </article>
    <?php endforeach; ?>
    <?php if (empty($destinos)): ?>
        <p>No hay destinos que coincidan con la busqueda.</p>
    <?php endif; ?>
</div>

<?php require BASE_PATH . '/vistas/layouts/footer.php'; ?>
