<?php require BASE_PATH . '/vistas/layouts/header.php'; ?>

<!-- Busqueda de hoteles para clientes. -->
<section class="admin-encabezado">
    <div>
        <h1>Hoteles</h1>
        <p>Busca hospedajes por destino, categoria o texto.</p>
    </div>
    <a class="boton boton--compacto boton--primario" href="<?php echo BASE_URL; ?>/reservar.php">Crear reserva</a>
</section>

<?php require BASE_PATH . '/vistas/layouts/mensajes.php'; ?>

<!-- Filtros del catalogo de hoteles. -->
<form class="filtros" method="get" action="<?php echo BASE_URL; ?>/hoteles.php">
    <input type="text" name="busqueda" placeholder="Buscar hotel, destino o direccion" value="<?php echo htmlspecialchars($busqueda, ENT_QUOTES); ?>">
    <select name="destino">
        <option value="0">Todos los destinos</option>
        <?php foreach ($destinos as $destino): ?>
            <option value="<?php echo (int) $destino['id_destino']; ?>" <?php echo (int) $idDestino === (int) $destino['id_destino'] ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($destino['nombre'], ENT_QUOTES); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <select name="categoria">
        <option value="0">Todas las categorias</option>
        <?php for ($i = 1; $i <= 5; $i++): ?>
            <option value="<?php echo $i; ?>" <?php echo (int) $categoria === $i ? 'selected' : ''; ?>><?php echo $i; ?> estrellas</option>
        <?php endfor; ?>
    </select>
    <button class="boton boton--compacto" type="submit">Buscar</button>
</form>

<!-- Resultados de hoteles disponibles. -->
<div class="catalogo-grid">
    <?php foreach ($hoteles as $hotel): ?>
        <article class="catalogo-item">
            <div class="catalogo-item__media">
                <?php if (!empty($hotel['imagen'])): ?>
                    <img onerror="this.style.display='none';" src="<?php echo htmlspecialchars($hotel['imagen'], ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($hotel['nombre'], ENT_QUOTES); ?>">
                <?php else: ?>
                    <span><?php echo htmlspecialchars(substr($hotel['nombre'], 0, 1), ENT_QUOTES); ?></span>
                <?php endif; ?>
            </div>
            <div class="catalogo-item__contenido">
                <h2><?php echo htmlspecialchars($hotel['nombre'], ENT_QUOTES); ?></h2>
                <p class="texto-muted"><?php echo htmlspecialchars($hotel['destino_nombre'], ENT_QUOTES); ?> · <?php echo (int) $hotel['categoria']; ?> estrellas</p>
                <p><?php echo htmlspecialchars($hotel['descripcion'], ENT_QUOTES); ?></p>
                <p><strong>CRC <?php echo number_format((float) $hotel['precio_noche'], 2); ?></strong> por noche</p>
                <div class="catalogo-item__acciones">
                    <a href="<?php echo BASE_URL; ?>/reservar.php?destino=<?php echo (int) $hotel['id_destino']; ?>&hotel=<?php echo (int) $hotel['id_hotel']; ?>">Reservar este hotel</a>
                </div>
            </div>
        </article>
    <?php endforeach; ?>
    <?php if (empty($hoteles)): ?>
        <p>No hay hoteles que coincidan con la busqueda.</p>
    <?php endif; ?>
</div>

<?php require BASE_PATH . '/vistas/layouts/footer.php'; ?>
