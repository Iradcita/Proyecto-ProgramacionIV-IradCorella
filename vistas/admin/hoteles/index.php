<?php require BASE_PATH . '/vistas/layouts/header.php'; ?>

<!-- Encabezado del mantenimiento de hoteles. -->
<section class="admin-encabezado">
    <div>
        <h1>Hoteles</h1>
        <p>Administra hospedajes, precios, habitaciones y datos de contacto.</p>
    </div>
    <a class="boton boton--compacto boton--primario" href="<?php echo BASE_URL; ?>/admin_hoteles.php?accion=nuevo">Nuevo hotel</a>
</section>

<?php require BASE_PATH . '/vistas/layouts/mensajes.php'; ?>

<!-- Filtros para encontrar hoteles rapidamente. -->
<form class="filtros" method="get" action="<?php echo BASE_URL; ?>/admin_hoteles.php">
    <input type="text" name="busqueda" placeholder="Buscar hotel o destino" value="<?php echo htmlspecialchars($busqueda, ENT_QUOTES); ?>">
    <select name="destino">
        <option value="0">Todos los destinos</option>
        <?php foreach ($destinos as $destino): ?>
            <option value="<?php echo (int) $destino['id_destino']; ?>" <?php echo (int) $idDestino === (int) $destino['id_destino'] ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($destino['nombre'], ENT_QUOTES); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button class="boton boton--compacto" type="submit">Filtrar</button>
</form>

<!-- Tabla del CRUD de hoteles. -->
<div class="tabla-contenedor">
    <table class="tabla-admin">
        <thead>
            <tr>
                <th>Hotel</th>
                <th>Destino</th>
                <th>Categoria</th>
                <th>Precio noche</th>
                <th>Habitaciones</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($hoteles as $hotel): ?>
                <tr>
                    <td><?php echo htmlspecialchars($hotel['nombre'], ENT_QUOTES); ?></td>
                    <td><?php echo htmlspecialchars($hotel['destino_nombre'], ENT_QUOTES); ?></td>
                    <td><?php echo (int) $hotel['categoria']; ?> estrellas</td>
                    <td>CRC <?php echo number_format((float) $hotel['precio_noche'], 2); ?></td>
                    <td><?php echo (int) $hotel['cantidad_habitaciones']; ?></td>
                    <td><span class="estado <?php echo (int) $hotel['estado'] === 1 ? 'estado--activo' : 'estado--inactivo'; ?>"><?php echo (int) $hotel['estado'] === 1 ? 'Activo' : 'Inactivo'; ?></span></td>
                    <td class="acciones">
                        <a href="<?php echo BASE_URL; ?>/admin_hoteles.php?accion=editar&id=<?php echo (int) $hotel['id_hotel']; ?>">Editar</a>
                        <form method="post" action="<?php echo BASE_URL; ?>/admin_hoteles.php?accion=eliminar">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">
                            <input type="hidden" name="id_hotel" value="<?php echo (int) $hotel['id_hotel']; ?>">
                            <button type="submit">Desactivar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($hoteles)): ?>
                <tr><td colspan="7">No hay hoteles para mostrar.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require BASE_PATH . '/vistas/layouts/footer.php'; ?>
