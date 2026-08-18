<?php require BASE_PATH . '/vistas/layouts/header.php'; ?>

<!-- Encabezado del modulo de actividades. -->
<section class="admin-encabezado">
    <div>
        <h1>Actividades</h1>
        <p>Administra tours, precios, duracion y cupos disponibles.</p>
    </div>
    <a class="boton boton--compacto boton--primario" href="<?php echo BASE_URL; ?>/admin_actividades.php?accion=nuevo">Nueva actividad</a>
</section>

<?php require BASE_PATH . '/vistas/layouts/mensajes.php'; ?>

<!-- Busqueda de actividades por texto o destino. -->
<form class="filtros" method="get" action="<?php echo BASE_URL; ?>/admin_actividades.php">
    <input type="text" name="busqueda" placeholder="Buscar actividad o destino" value="<?php echo htmlspecialchars($busqueda, ENT_QUOTES); ?>">
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

<!-- Tabla administrativa de actividades. -->
<div class="tabla-contenedor">
    <table class="tabla-admin">
        <thead>
            <tr>
                <th>Actividad</th>
                <th>Destino</th>
                <th>Precio</th>
                <th>Duracion</th>
                <th>Cupo</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($actividades as $actividad): ?>
                <tr>
                    <td><?php echo htmlspecialchars($actividad['nombre'], ENT_QUOTES); ?></td>
                    <td><?php echo htmlspecialchars($actividad['destino_nombre'], ENT_QUOTES); ?></td>
                    <td>CRC <?php echo number_format((float) $actividad['precio'], 2); ?></td>
                    <td><?php echo (int) $actividad['duracion_minutos']; ?> min</td>
                    <td><?php echo (int) $actividad['cupo_maximo']; ?></td>
                    <td><span class="estado <?php echo (int) $actividad['estado'] === 1 ? 'estado--activo' : 'estado--inactivo'; ?>"><?php echo (int) $actividad['estado'] === 1 ? 'Activo' : 'Inactivo'; ?></span></td>
                    <td class="acciones">
                        <a href="<?php echo BASE_URL; ?>/admin_actividades.php?accion=editar&id=<?php echo (int) $actividad['id_actividad']; ?>">Editar</a>
                        <form method="post" action="<?php echo BASE_URL; ?>/admin_actividades.php?accion=eliminar" onsubmit="return confirm('Seguro que deseas desactivar esta actividad?');">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">
                            <input type="hidden" name="id_actividad" value="<?php echo (int) $actividad['id_actividad']; ?>">
                            <button type="submit">Desactivar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($actividades)): ?>
                <tr><td colspan="7">No hay actividades para mostrar.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require BASE_PATH . '/vistas/layouts/footer.php'; ?>
