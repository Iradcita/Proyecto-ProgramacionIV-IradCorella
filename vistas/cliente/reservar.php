<?php require BASE_PATH . '/vistas/layouts/header.php'; ?>

<!-- Formulario de reserva para el cliente conectado. -->
<section class="formulario-admin">
    <div class="admin-encabezado">
        <div>
            <h1>Crear reservacion</h1>
            <p>Selecciona hotel, fechas, personas y actividades opcionales.</p>
        </div>
        <a class="boton boton--compacto" href="<?php echo BASE_URL; ?>/mis_reservaciones.php">Mis reservaciones</a>
    </div>

    <?php require BASE_PATH . '/vistas/layouts/mensajes.php'; ?>

    <!-- Filtro rapido por destino para recargar hoteles y actividades relacionados. -->
    <form class="filtros" method="get" action="<?php echo BASE_URL; ?>/reservar.php">
        <select name="destino">
            <option value="0">Todos los destinos</option>
            <?php foreach ($destinos as $destino): ?>
                <option value="<?php echo (int) $destino['id_destino']; ?>" <?php echo (int) $idDestino === (int) $destino['id_destino'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($destino['nombre'], ENT_QUOTES); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button class="boton boton--compacto" type="submit">Aplicar destino</button>
    </form>

    <form method="post" action="<?php echo BASE_URL; ?>/reservar.php">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">

        <div class="grid-formulario">
            <div class="campo campo--ancho">
                <label for="id_hotel">Hotel</label>
                <select id="id_hotel" name="id_hotel" required>
                    <option value="">Seleccione</option>
                    <?php foreach ($hoteles as $hotel): ?>
                        <option value="<?php echo (int) $hotel['id_hotel']; ?>"
                                data-destino="<?php echo (int) $hotel['id_destino']; ?>"
                                <?php echo (int) $hotelPreseleccionado === (int) $hotel['id_hotel'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($hotel['destino_nombre'] . ' - ' . $hotel['nombre'] . ' (CRC ' . number_format((float) $hotel['precio_noche'], 2) . ' por noche)', ENT_QUOTES); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (empty($hoteles)): ?>
                    <small>No hay hoteles disponibles para el filtro actual.</small>
                <?php endif; ?>
            </div>
            <div class="campo">
                <label for="fecha_inicio">Fecha inicio</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" required>
            </div>
            <div class="campo">
                <label for="fecha_fin">Fecha fin</label>
                <input type="date" id="fecha_fin" name="fecha_fin" required>
            </div>
            <div class="campo">
                <label for="cantidad_personas">Personas</label>
                <input type="number" id="cantidad_personas" name="cantidad_personas" min="1" value="1" required>
            </div>
            <div class="campo">
                <label for="cantidad_habitaciones">Habitaciones</label>
                <input type="number" id="cantidad_habitaciones" name="cantidad_habitaciones" min="1" value="1" required>
            </div>
            <div class="campo campo--ancho">
                <label>Actividades opcionales</label>
                <small>Solo se muestran las actividades del mismo destino que el hotel elegido.</small>
                <div class="checkbox-lista">
                    <?php foreach ($actividades as $actividad): ?>
                        <label data-destino="<?php echo (int) $actividad['id_destino']; ?>">
                            <input type="checkbox" name="actividades[]" value="<?php echo (int) $actividad['id_actividad']; ?>">
                            <?php echo htmlspecialchars($actividad['destino_nombre'] . ' - ' . $actividad['nombre'] . ' (CRC ' . number_format((float) $actividad['precio'], 2) . ')', ENT_QUOTES); ?>
                        </label>
                    <?php endforeach; ?>
                    <?php if (empty($actividades)): ?>
                        <p>No hay actividades disponibles para el filtro actual.</p>
                    <?php endif; ?>
                    <!-- Aviso que muestra el JavaScript si el destino elegido no tiene actividades -->
                    <p id="avisoSinActividades" hidden>Este destino todavia no tiene actividades disponibles.</p>
                </div>
            </div>
            <div class="campo campo--ancho">
                <label for="observaciones">Observaciones</label>
                <textarea id="observaciones" name="observaciones" maxlength="1000"></textarea>
            </div>
        </div>

        <button class="boton boton--primario" type="submit">Confirmar solicitud</button>
    </form>
</section>

<script src="<?php echo RECURSOS_URL; ?>/js/reservar.js"></script>

<?php require BASE_PATH . '/vistas/layouts/footer.php'; ?>
