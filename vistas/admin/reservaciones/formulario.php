<?php require BASE_PATH . '/vistas/layouts/header.php'; ?>

<?php
// Valores para reutilizar el formulario en creacion y edicion.
$editando = !empty($reservacion);
$hotelSeleccionado = $reservacionHotel['id_hotel'] ?? '';
$habitacionesSeleccionadas = $reservacionHotel['cantidad_habitaciones'] ?? 1;
?>

<!-- Formulario administrativo de reservaciones. -->
<section class="formulario-admin">
    <div class="admin-encabezado">
        <div>
            <h1><?php echo $editando ? 'Editar reservacion' : 'Nueva reservacion'; ?></h1>
            <p>El total se calcula con noches de hotel y actividades por persona.</p>
        </div>
        <a class="boton boton--compacto" href="<?php echo BASE_URL; ?>/admin_reservaciones.php">Volver</a>
    </div>

    <?php require BASE_PATH . '/vistas/layouts/mensajes.php'; ?>

    <form method="post" action="<?php echo BASE_URL; ?>/admin_reservaciones.php?accion=guardar">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">
        <input type="hidden" name="id_reservacion" value="<?php echo $editando ? (int) $reservacion['id_reservacion'] : 0; ?>">

        <div class="grid-formulario">
            <div class="campo">
                <label for="id_usuario">Cliente</label>
                <select id="id_usuario" name="id_usuario" required>
                    <option value="">Seleccione</option>
                    <?php foreach ($clientes as $cliente): ?>
                        <option value="<?php echo (int) $cliente['id_usuario']; ?>" <?php echo $editando && (int) $reservacion['id_usuario'] === (int) $cliente['id_usuario'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellidos'] . ' - ' . $cliente['correo'], ENT_QUOTES); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo">
                <label for="id_hotel">Hotel</label>
                <select id="id_hotel" name="id_hotel" required>
                    <option value="">Seleccione</option>
                    <?php foreach ($hoteles as $hotel): ?>
                        <option value="<?php echo (int) $hotel['id_hotel']; ?>" <?php echo (int) $hotelSeleccionado === (int) $hotel['id_hotel'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($hotel['destino_nombre'] . ' - ' . $hotel['nombre'] . ' (CRC ' . number_format((float) $hotel['precio_noche'], 2) . ')', ENT_QUOTES); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo">
                <label for="fecha_inicio">Fecha inicio</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" required value="<?php echo htmlspecialchars($reservacion['fecha_inicio'] ?? '', ENT_QUOTES); ?>">
            </div>
            <div class="campo">
                <label for="fecha_fin">Fecha fin</label>
                <input type="date" id="fecha_fin" name="fecha_fin" required value="<?php echo htmlspecialchars($reservacion['fecha_fin'] ?? '', ENT_QUOTES); ?>">
            </div>
            <div class="campo">
                <label for="cantidad_personas">Personas</label>
                <input type="number" id="cantidad_personas" name="cantidad_personas" min="1" required value="<?php echo htmlspecialchars($reservacion['cantidad_personas'] ?? 1, ENT_QUOTES); ?>">
            </div>
            <div class="campo">
                <label for="cantidad_habitaciones">Habitaciones</label>
                <input type="number" id="cantidad_habitaciones" name="cantidad_habitaciones" min="1" required value="<?php echo htmlspecialchars($habitacionesSeleccionadas, ENT_QUOTES); ?>">
            </div>
            <div class="campo">
                <label for="estado">Estado</label>
                <select id="estado" name="estado">
                    <?php foreach (array('pendiente', 'confirmada', 'cancelada', 'completada') as $estadoOpcion): ?>
                        <option value="<?php echo $estadoOpcion; ?>" <?php echo ($reservacion['estado'] ?? 'pendiente') === $estadoOpcion ? 'selected' : ''; ?>>
                            <?php echo ucfirst($estadoOpcion); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo campo--ancho">
                <label>Actividades</label>
                <div class="checkbox-lista">
                    <?php foreach ($actividades as $actividad): ?>
                        <label>
                            <input type="checkbox" name="actividades[]" value="<?php echo (int) $actividad['id_actividad']; ?>" <?php echo in_array((int) $actividad['id_actividad'], $actividadesSeleccionadas, true) ? 'checked' : ''; ?>>
                            <?php echo htmlspecialchars($actividad['destino_nombre'] . ' - ' . $actividad['nombre'] . ' (CRC ' . number_format((float) $actividad['precio'], 2) . ')', ENT_QUOTES); ?>
                        </label>
                    <?php endforeach; ?>
                    <?php if (empty($actividades)): ?>
                        <p>No hay actividades activas disponibles.</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="campo campo--ancho">
                <label for="observaciones">Observaciones</label>
                <textarea id="observaciones" name="observaciones" maxlength="1000"><?php echo htmlspecialchars($reservacion['observaciones'] ?? '', ENT_QUOTES); ?></textarea>
            </div>
        </div>

        <button class="boton boton--primario" type="submit">Guardar reservacion</button>
    </form>
</section>

<?php require BASE_PATH . '/vistas/layouts/footer.php'; ?>
