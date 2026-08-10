<?php require BASE_PATH . '/vistas/layouts/header.php'; ?>

<!-- Encabezado de la bitacora administrativa. -->
<section class="admin-encabezado">
    <div>
        <h1>Bitacora</h1>
        <p>Consulta las acciones recientes registradas por el sistema.</p>
    </div>
</section>

<?php require BASE_PATH . '/vistas/layouts/mensajes.php'; ?>

<!-- Filtro de acciones por usuario, correo, tabla o accion. -->
<form class="filtros" method="get" action="<?php echo BASE_URL; ?>/admin_bitacora.php">
    <input type="text" name="busqueda" placeholder="Buscar accion, tabla o usuario" value="<?php echo htmlspecialchars($busqueda, ENT_QUOTES); ?>">
    <button class="boton boton--compacto" type="submit">Filtrar</button>
</form>

<!-- Tabla de auditoria interna. -->
<div class="tabla-contenedor">
    <table class="tabla-admin">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Usuario</th>
                <th>Accion</th>
                <th>Tabla</th>
                <th>Registro</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($acciones as $accion): ?>
                <tr>
                    <td><?php echo htmlspecialchars($accion['fecha'], ENT_QUOTES); ?></td>
                    <td>
                        <?php echo htmlspecialchars($accion['usuario_nombre'], ENT_QUOTES); ?><br>
                        <small><?php echo htmlspecialchars($accion['correo'], ENT_QUOTES); ?></small>
                    </td>
                    <td><?php echo htmlspecialchars($accion['accion'], ENT_QUOTES); ?></td>
                    <td><?php echo htmlspecialchars($accion['tabla_afectada'] ?? '-', ENT_QUOTES); ?></td>
                    <td><?php echo htmlspecialchars((string) ($accion['id_registro_afectado'] ?? '-'), ENT_QUOTES); ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($acciones)): ?>
                <tr><td colspan="5">No hay acciones registradas.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require BASE_PATH . '/vistas/layouts/footer.php'; ?>
