<?php require BASE_PATH . '/vistas/layouts/header.php'; ?>

<!-- Encabezado con accion principal del CRUD de destinos. -->
<section class="admin-encabezado">
    <div>
        <h1>Destinos</h1>
        <p>Administra los destinos turisticos disponibles en el sistema.</p>
    </div>
    <a class="boton boton--compacto boton--primario" href="<?php echo BASE_URL; ?>/admin_destinos.php?accion=nuevo">Nuevo destino</a>
</section>

<?php require BASE_PATH . '/vistas/layouts/mensajes.php'; ?>

<!-- Filtros de busqueda para nombre y provincia. -->
<form class="filtros" method="get" action="<?php echo BASE_URL; ?>/admin_destinos.php">
    <input type="text" name="busqueda" placeholder="Buscar destino o provincia" value="<?php echo htmlspecialchars($busqueda, ENT_QUOTES); ?>">
    <select name="provincia">
        <option value="0">Todas las provincias</option>
        <?php foreach ($provincias as $provincia): ?>
            <option value="<?php echo (int) $provincia['id_provincia']; ?>" <?php echo (int) $idProvincia === (int) $provincia['id_provincia'] ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($provincia['nombre'], ENT_QUOTES); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button class="boton boton--compacto" type="submit">Filtrar</button>
</form>

<!-- Tabla principal del CRUD. -->
<div class="tabla-contenedor">
    <table class="tabla-admin">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Provincia</th>
                <th>Ubicacion</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($destinos as $destino): ?>
                <tr>
                    <td><?php echo htmlspecialchars($destino['nombre'], ENT_QUOTES); ?></td>
                    <td><?php echo htmlspecialchars($destino['provincia_nombre'], ENT_QUOTES); ?></td>
                    <td><?php echo htmlspecialchars(($destino['latitud'] ?? '-') . ' / ' . ($destino['longitud'] ?? '-'), ENT_QUOTES); ?></td>
                    <td><span class="estado <?php echo (int) $destino['estado'] === 1 ? 'estado--activo' : 'estado--inactivo'; ?>"><?php echo (int) $destino['estado'] === 1 ? 'Activo' : 'Inactivo'; ?></span></td>
                    <td class="acciones">
                        <a href="<?php echo BASE_URL; ?>/admin_destinos.php?accion=editar&id=<?php echo (int) $destino['id_destino']; ?>">Editar</a>
                        <form method="post" action="<?php echo BASE_URL; ?>/admin_destinos.php?accion=eliminar">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">
                            <input type="hidden" name="id_destino" value="<?php echo (int) $destino['id_destino']; ?>">
                            <button type="submit">Desactivar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($destinos)): ?>
                <tr><td colspan="5">No hay destinos para mostrar.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require BASE_PATH . '/vistas/layouts/footer.php'; ?>
