<?php require BASE_PATH . '/vistas/layouts/header.php'; ?>

<!-- Encabezado del modulo de reservaciones. -->
<section class="admin-encabezado">
    <div>
        <h1>Reservaciones</h1>
        <p>Consulta, edita y cancela reservaciones de clientes.</p>
    </div>
    <a class="boton boton--compacto boton--primario" href="<?php echo BASE_URL; ?>/admin_reservaciones.php?accion=nuevo">Nueva reservacion</a>
</section>

<?php require BASE_PATH . '/vistas/layouts/mensajes.php'; ?>

<!-- Filtros por codigo, cliente, correo y estado. -->
<form class="filtros" method="get" action="<?php echo BASE_URL; ?>/admin_reservaciones.php">
    <input type="text" name="busqueda" placeholder="Buscar codigo, cliente o correo" value="<?php echo htmlspecialchars($busqueda, ENT_QUOTES); ?>">
    <select name="estado">
        <option value="">Todos los estados</option>
        <?php foreach (array('pendiente', 'confirmada', 'cancelada', 'completada') as $estadoOpcion): ?>
            <option value="<?php echo $estadoOpcion; ?>" <?php echo $estado === $estadoOpcion ? 'selected' : ''; ?>>
                <?php echo ucfirst($estadoOpcion); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button class="boton boton--compacto" type="submit">Filtrar</button>
</form>

<!-- Tabla de reservaciones con resumen del detalle. -->
<div class="tabla-contenedor">
    <table class="tabla-admin">
        <thead>
            <tr>
                <th>Codigo</th>
                <th>Cliente</th>
                <th>Hotel</th>
                <th>Fechas</th>
                <th>Personas</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservaciones as $reservacion): ?>
                <tr>
                    <td><?php echo htmlspecialchars($reservacion['codigo'], ENT_QUOTES); ?></td>
                    <td>
                        <?php echo htmlspecialchars($reservacion['cliente_nombre'], ENT_QUOTES); ?><br>
                        <small><?php echo htmlspecialchars($reservacion['cliente_correo'], ENT_QUOTES); ?></small>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($reservacion['hotel_nombre'] ?? 'Sin hotel', ENT_QUOTES); ?><br>
                        <small><?php echo (int) $reservacion['total_actividades']; ?> actividades</small>
                    </td>
                    <td><?php echo htmlspecialchars($reservacion['fecha_inicio'] . ' a ' . $reservacion['fecha_fin'], ENT_QUOTES); ?></td>
                    <td><?php echo (int) $reservacion['cantidad_personas']; ?></td>
                    <td>CRC <?php echo number_format((float) $reservacion['total'], 2); ?></td>
                    <td><span class="estado estado--<?php echo htmlspecialchars($reservacion['estado'], ENT_QUOTES); ?>"><?php echo htmlspecialchars(ucfirst($reservacion['estado']), ENT_QUOTES); ?></span></td>
                    <td class="acciones">
                        <a href="<?php echo BASE_URL; ?>/admin_reservaciones.php?accion=editar&id=<?php echo (int) $reservacion['id_reservacion']; ?>">Editar</a>
                        <form method="post" action="<?php echo BASE_URL; ?>/admin_reservaciones.php?accion=eliminar">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">
                            <input type="hidden" name="id_reservacion" value="<?php echo (int) $reservacion['id_reservacion']; ?>">
                            <button type="submit">Cancelar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($reservaciones)): ?>
                <tr><td colspan="8">No hay reservaciones para mostrar.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require BASE_PATH . '/vistas/layouts/footer.php'; ?>
