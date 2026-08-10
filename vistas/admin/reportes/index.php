<?php require BASE_PATH . '/vistas/layouts/header.php'; ?>

<?php
// Datos compactos para que JavaScript pinte graficos sin librerias externas.
$datosGraficos = array(
    'destinos' => array_map(function ($fila) {
        return array('label' => $fila['destino'], 'value' => (int) $fila['total_reservaciones']);
    }, $reservacionesPorDestino),
    'hoteles' => array_map(function ($fila) {
        return array('label' => $fila['hotel'], 'value' => (int) $fila['total_reservas']);
    }, $hotelesMasReservados),
    'actividades' => array_map(function ($fila) {
        return array('label' => $fila['actividad'], 'value' => (int) $fila['total_solicitudes']);
    }, $actividadesMasSolicitadas),
    'fechas' => array_map(function ($fila) {
        return array('label' => $fila['fecha'], 'value' => (int) $fila['total_reservaciones']);
    }, array_reverse($reservacionesPorFecha)),
);
?>

<!-- Encabezado del panel de reportes. -->
<section class="admin-encabezado">
    <div>
        <h1>Reportes</h1>
        <p>Indicadores de reservaciones, usuarios, ingresos y datos externos.</p>
    </div>
</section>

<?php require BASE_PATH . '/vistas/layouts/mensajes.php'; ?>

<!-- Tarjetas de resumen general. -->
<section class="reporte-resumen">
    <article>
        <span>Reservaciones</span>
        <strong><?php echo (int) $resumen['total_reservaciones']; ?></strong>
    </article>
    <article>
        <span>Usuarios</span>
        <strong><?php echo (int) $resumen['total_usuarios']; ?></strong>
    </article>
    <article>
        <span>Hoteles activos</span>
        <strong><?php echo (int) $resumen['hoteles_activos']; ?></strong>
    </article>
    <article>
        <span>Actividades activas</span>
        <strong><?php echo (int) $resumen['actividades_activas']; ?></strong>
    </article>
    <article>
        <span>Ingresos estimados</span>
        <strong>CRC <?php echo number_format((float) $resumen['ingresos_estimados'], 2); ?></strong>
    </article>
</section>

<!-- Datos consumidos desde APIs REST publicas. -->
<section class="api-grid">
    <article class="detalle-bloque">
        <h2>Clima actual</h2>
        <p class="texto-muted"><?php echo htmlspecialchars($destinoClima['nombre'], ENT_QUOTES); ?></p>
        <?php if ($clima): ?>
            <p><strong><?php echo htmlspecialchars((string) $clima['temperature_2m'], ENT_QUOTES); ?> C</strong></p>
            <p>Humedad: <?php echo htmlspecialchars((string) $clima['relative_humidity_2m'], ENT_QUOTES); ?>%</p>
            <p>Viento: <?php echo htmlspecialchars((string) $clima['wind_speed_10m'], ENT_QUOTES); ?> km/h</p>
            <small>Fuente: Open-Meteo</small>
        <?php else: ?>
            <p>No fue posible consultar el clima en este momento.</p>
        <?php endif; ?>
    </article>
    <article class="detalle-bloque">
        <h2>Tipo de cambio</h2>
        <p class="texto-muted">USD a CRC</p>
        <?php if ($tipoCambio): ?>
            <p><strong>CRC <?php echo number_format((float) $tipoCambio['rate'], 2); ?></strong></p>
            <p>Fecha: <?php echo htmlspecialchars($tipoCambio['date'], ENT_QUOTES); ?></p>
            <small>Fuente: Frankfurter</small>
        <?php else: ?>
            <p>No fue posible consultar el tipo de cambio en este momento.</p>
        <?php endif; ?>
    </article>
</section>

<!-- Barra de busqueda funcional por JavaScript. -->
<section class="filtros reporte-filtros">
    <input type="search" id="buscadorReportes" placeholder="Filtrar tablas de reportes">
    <button class="boton boton--compacto" type="button" id="limpiarReportes">Limpiar</button>
</section>

<!-- Graficos generados con JavaScript nativo. -->
<section class="graficos-grid">
    <article class="detalle-bloque">
        <h2>Reservaciones por destino</h2>
        <div class="grafico-barras" data-chart="destinos"></div>
    </article>
    <article class="detalle-bloque">
        <h2>Hoteles mas reservados</h2>
        <div class="grafico-barras" data-chart="hoteles"></div>
    </article>
    <article class="detalle-bloque">
        <h2>Actividades mas solicitadas</h2>
        <div class="grafico-barras" data-chart="actividades"></div>
    </article>
    <article class="detalle-bloque">
        <h2>Reservas por fecha</h2>
        <div class="grafico-barras" data-chart="fechas"></div>
    </article>
</section>

<script type="application/json" id="datosReportes"><?php echo json_encode($datosGraficos); ?></script>

<!-- Tablas requeridas por el enunciado. -->
<section class="reportes-tablas">
    <article class="detalle-bloque">
        <h2>Reservaciones por destino</h2>
        <div class="tabla-contenedor">
            <table class="tabla-admin tabla-reporte">
                <thead><tr><th>Destino</th><th>Reservaciones</th><th>Ingresos</th></tr></thead>
                <tbody>
                    <?php foreach ($reservacionesPorDestino as $fila): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($fila['destino'], ENT_QUOTES); ?></td>
                            <td><?php echo (int) $fila['total_reservaciones']; ?></td>
                            <td>CRC <?php echo number_format((float) $fila['ingresos'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </article>

    <article class="detalle-bloque">
        <h2>Hoteles mas reservados</h2>
        <div class="tabla-contenedor">
            <table class="tabla-admin tabla-reporte">
                <thead><tr><th>Hotel</th><th>Destino</th><th>Reservas</th><th>Ingresos hotel</th></tr></thead>
                <tbody>
                    <?php foreach ($hotelesMasReservados as $fila): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($fila['hotel'], ENT_QUOTES); ?></td>
                            <td><?php echo htmlspecialchars($fila['destino'], ENT_QUOTES); ?></td>
                            <td><?php echo (int) $fila['total_reservas']; ?></td>
                            <td>CRC <?php echo number_format((float) $fila['ingresos_hotel'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </article>

    <article class="detalle-bloque">
        <h2>Actividades mas solicitadas</h2>
        <div class="tabla-contenedor">
            <table class="tabla-admin tabla-reporte">
                <thead><tr><th>Actividad</th><th>Destino</th><th>Solicitudes</th><th>Personas</th><th>Ingresos</th></tr></thead>
                <tbody>
                    <?php foreach ($actividadesMasSolicitadas as $fila): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($fila['actividad'], ENT_QUOTES); ?></td>
                            <td><?php echo htmlspecialchars($fila['destino'], ENT_QUOTES); ?></td>
                            <td><?php echo (int) $fila['total_solicitudes']; ?></td>
                            <td><?php echo (int) $fila['total_personas']; ?></td>
                            <td>CRC <?php echo number_format((float) $fila['ingresos_actividad'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </article>

    <article class="detalle-bloque">
        <h2>Usuarios registrados</h2>
        <div class="tabla-contenedor">
            <table class="tabla-admin tabla-reporte">
                <thead><tr><th>Rol</th><th>Estado</th><th>Total</th></tr></thead>
                <tbody>
                    <?php foreach ($usuariosRegistrados as $fila): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($fila['rol'], ENT_QUOTES); ?></td>
                            <td><?php echo htmlspecialchars($fila['estado'], ENT_QUOTES); ?></td>
                            <td><?php echo (int) $fila['total_usuarios']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </article>

    <article class="detalle-bloque">
        <h2>Reservaciones por fecha</h2>
        <div class="tabla-contenedor">
            <table class="tabla-admin tabla-reporte">
                <thead><tr><th>Fecha</th><th>Reservaciones</th><th>Ingresos</th></tr></thead>
                <tbody>
                    <?php foreach ($reservacionesPorFecha as $fila): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($fila['fecha'], ENT_QUOTES); ?></td>
                            <td><?php echo (int) $fila['total_reservaciones']; ?></td>
                            <td>CRC <?php echo number_format((float) $fila['ingresos'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </article>

    <article class="detalle-bloque">
        <h2>Ingresos estimados</h2>
        <div class="tabla-contenedor">
            <table class="tabla-admin tabla-reporte">
                <thead><tr><th>Estado</th><th>Reservaciones</th><th>Total</th></tr></thead>
                <tbody>
                    <?php foreach ($ingresosEstimados as $fila): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($fila['estado'], ENT_QUOTES); ?></td>
                            <td><?php echo (int) $fila['total_reservaciones']; ?></td>
                            <td>CRC <?php echo number_format((float) $fila['total_ingresos'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </article>
</section>

<script src="<?php echo RECURSOS_URL; ?>/js/reportes.js"></script>

<?php require BASE_PATH . '/vistas/layouts/footer.php'; ?>
