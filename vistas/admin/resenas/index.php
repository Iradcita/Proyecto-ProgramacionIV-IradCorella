<?php require BASE_PATH . '/vistas/layouts/header.php'; ?>

<!-- Encabezado de moderacion de resenas. -->
<section class="admin-encabezado">
    <div>
        <h1>Resenas</h1>
        <p>Aprueba o rechaza comentarios enviados por clientes.</p>
    </div>
</section>

<?php require BASE_PATH . '/vistas/layouts/mensajes.php'; ?>

<!-- Filtro por estado de moderacion. -->
<form class="filtros" method="get" action="<?php echo BASE_URL; ?>/admin_resenas.php">
    <select name="estado">
        <option value="">Todos los estados</option>
        <?php foreach (array('pendiente', 'aprobada', 'rechazada') as $opcion): ?>
            <option value="<?php echo $opcion; ?>" <?php echo $estado === $opcion ? 'selected' : ''; ?>><?php echo ucfirst($opcion); ?></option>
        <?php endforeach; ?>
    </select>
    <button class="boton boton--compacto" type="submit">Filtrar</button>
</form>

<!-- Tabla para moderar resenas. -->
<div class="tabla-contenedor">
    <table class="tabla-admin">
        <thead>
            <tr>
                <th>Destino</th>
                <th>Cliente</th>
                <th>Calificacion</th>
                <th>Comentario</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($resenas as $resena): ?>
                <tr>
                    <td><?php echo htmlspecialchars($resena['destino_nombre'], ENT_QUOTES); ?></td>
                    <td>
                        <?php echo htmlspecialchars($resena['usuario_nombre'], ENT_QUOTES); ?><br>
                        <small><?php echo htmlspecialchars($resena['correo'], ENT_QUOTES); ?></small>
                    </td>
                    <td><?php echo (int) $resena['calificacion']; ?>/5</td>
                    <td><?php echo htmlspecialchars($resena['comentario'] ?? '-', ENT_QUOTES); ?></td>
                    <td><span class="estado estado--<?php echo htmlspecialchars($resena['estado'], ENT_QUOTES); ?>"><?php echo htmlspecialchars(ucfirst($resena['estado']), ENT_QUOTES); ?></span></td>
                    <td class="acciones">
                        <form method="post" action="<?php echo BASE_URL; ?>/admin_resenas.php">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">
                            <input type="hidden" name="id_resena" value="<?php echo (int) $resena['id_resena']; ?>">
                            <input type="hidden" name="estado" value="aprobada">
                            <button type="submit">Aprobar</button>
                        </form>
                        <form method="post" action="<?php echo BASE_URL; ?>/admin_resenas.php">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">
                            <input type="hidden" name="id_resena" value="<?php echo (int) $resena['id_resena']; ?>">
                            <input type="hidden" name="estado" value="rechazada">
                            <button type="submit">Rechazar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($resenas)): ?>
                <tr><td colspan="6">No hay resenas para mostrar.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require BASE_PATH . '/vistas/layouts/footer.php'; ?>
