<?php require BASE_PATH . '/vistas/layouts/header.php'; ?>

<!-- Detalle completo de una reservacion del cliente. -->
<section class="admin-encabezado">
    <div>
        <h1>Reservacion <?php echo htmlspecialchars($reservacion['codigo'], ENT_QUOTES); ?></h1>
        <p>Estado actual: <span class="estado estado--<?php echo htmlspecialchars($reservacion['estado'], ENT_QUOTES); ?>"><?php echo htmlspecialchars(ucfirst($reservacion['estado']), ENT_QUOTES); ?></span></p>
    </div>
    <a class="boton boton--compacto" href="<?php echo BASE_URL; ?>/mis_reservaciones.php">Volver</a>
</section>

<?php require BASE_PATH . '/vistas/layouts/mensajes.php'; ?>

<!-- Resumen general de la reserva. -->
<section class="detalle-reserva-grid">
    <article class="detalle-bloque">
        <h2>Viaje</h2>
        <p><strong>Destino:</strong> <?php echo htmlspecialchars($reservacion['destino_nombre'] ?? '-', ENT_QUOTES); ?></p>
        <p><strong>Fechas:</strong> <?php echo htmlspecialchars($reservacion['fecha_inicio'] . ' a ' . $reservacion['fecha_fin'], ENT_QUOTES); ?></p>
        <p><strong>Personas:</strong> <?php echo (int) $reservacion['cantidad_personas']; ?></p>
        <p><strong>Total:</strong> CRC <?php echo number_format((float) $reservacion['total'], 2); ?></p>
    </article>

    <article class="detalle-bloque">
        <h2>Hotel</h2>
        <p><strong>Nombre:</strong> <?php echo htmlspecialchars($reservacion['hotel_nombre'] ?? '-', ENT_QUOTES); ?></p>
        <p><strong>Direccion:</strong> <?php echo htmlspecialchars($reservacion['hotel_direccion'] ?? '-', ENT_QUOTES); ?></p>
        <p><strong>Telefono:</strong> <?php echo htmlspecialchars($reservacion['hotel_telefono'] ?? '-', ENT_QUOTES); ?></p>
        <p><strong>Habitaciones:</strong> <?php echo (int) ($reservacion['cantidad_habitaciones'] ?? 0); ?></p>
        <p><strong>Subtotal:</strong> CRC <?php echo number_format((float) ($reservacion['subtotal_hotel'] ?? 0), 2); ?></p>
    </article>
</section>

<!-- Actividades agregadas a la reserva. -->
<section class="seccion-cliente">
    <div class="admin-encabezado">
        <div>
            <h2>Actividades</h2>
            <p>Detalle de experiencias seleccionadas.</p>
        </div>
    </div>

    <div class="tabla-contenedor">
        <table class="tabla-admin">
            <thead>
                <tr>
                    <th>Actividad</th>
                    <th>Destino</th>
                    <th>Fecha y hora</th>
                    <th>Personas</th>
                    <th>Precio persona</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($actividades as $actividad): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($actividad['actividad_nombre'], ENT_QUOTES); ?></td>
                        <td><?php echo htmlspecialchars($actividad['destino_nombre'], ENT_QUOTES); ?></td>
                        <td><?php echo htmlspecialchars($actividad['fecha_hora'], ENT_QUOTES); ?></td>
                        <td><?php echo (int) $actividad['cantidad_personas']; ?></td>
                        <td>CRC <?php echo number_format((float) $actividad['precio_persona_aplicado'], 2); ?></td>
                        <td>CRC <?php echo number_format((float) $actividad['subtotal'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($actividades)): ?>
                    <tr><td colspan="6">No agregaste actividades a esta reservacion.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php if (!empty($reservacion['observaciones'])): ?>
    <!-- Observaciones registradas por el cliente o administracion. -->
    <section class="detalle-bloque seccion-cliente">
        <h2>Observaciones</h2>
        <p><?php echo htmlspecialchars($reservacion['observaciones'], ENT_QUOTES); ?></p>
    </section>
<?php endif; ?>

<?php require BASE_PATH . '/vistas/layouts/footer.php'; ?>
