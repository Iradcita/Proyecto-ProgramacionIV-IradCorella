<?php require BASE_PATH . '/vistas/layouts/header.php'; ?>

<!-- Historial de reservaciones del cliente. -->
<section class="admin-encabezado">
    <div>
        <h1>Mis reservaciones</h1>
        <p>Consulta el estado y resumen de tus reservas.</p>
    </div>
    <a class="boton boton--compacto boton--primario" href="<?php echo BASE_URL; ?>/reservar.php">Nueva reservacion</a>
</section>

<?php require BASE_PATH . '/vistas/layouts/mensajes.php'; ?>

<!-- Tabla con reservas propias del usuario en sesion. -->
<div class="tabla-contenedor">
    <table class="tabla-admin">
        <thead>
            <tr>
                <th>Codigo</th>
                <th>Destino</th>
                <th>Hotel</th>
                <th>Fechas</th>
                <th>Personas</th>
                <th>Actividades</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Detalle</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservaciones as $reservacion): ?>
                <tr>
                    <td><?php echo htmlspecialchars($reservacion['codigo'], ENT_QUOTES); ?></td>
                    <td><?php echo htmlspecialchars($reservacion['destino_nombre'] ?? '-', ENT_QUOTES); ?></td>
                    <td><?php echo htmlspecialchars($reservacion['hotel_nombre'] ?? '-', ENT_QUOTES); ?></td>
                    <td><?php echo htmlspecialchars($reservacion['fecha_inicio'] . ' a ' . $reservacion['fecha_fin'], ENT_QUOTES); ?></td>
                    <td><?php echo (int) $reservacion['cantidad_personas']; ?></td>
                    <td><?php echo (int) $reservacion['total_actividades']; ?></td>
                    <td>CRC <?php echo number_format((float) $reservacion['total'], 2); ?></td>
                    <td><span class="estado estado--<?php echo htmlspecialchars($reservacion['estado'], ENT_QUOTES); ?>"><?php echo htmlspecialchars(ucfirst($reservacion['estado']), ENT_QUOTES); ?></span></td>
                    <td><a href="<?php echo BASE_URL; ?>/mis_reservaciones.php?accion=detalle&id=<?php echo (int) $reservacion['id_reservacion']; ?>">Ver</a></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($reservaciones)): ?>
                <tr><td colspan="9">Todavia no tienes reservaciones.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require BASE_PATH . '/vistas/layouts/footer.php'; ?>
