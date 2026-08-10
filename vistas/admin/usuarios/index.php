<?php require BASE_PATH . '/vistas/layouts/header.php'; ?>

<!-- Encabezado de administracion de usuarios. -->
<section class="admin-encabezado">
    <div>
        <h1>Usuarios</h1>
        <p>Administra cuentas, roles y estados de acceso.</p>
    </div>
    <a class="boton boton--compacto boton--primario" href="<?php echo BASE_URL; ?>/admin_usuarios.php?accion=nuevo">Nuevo usuario</a>
</section>

<?php require BASE_PATH . '/vistas/layouts/mensajes.php'; ?>

<!-- Buscador por nombre, apellidos o correo. -->
<form class="filtros" method="get" action="<?php echo BASE_URL; ?>/admin_usuarios.php">
    <input type="text" name="busqueda" placeholder="Buscar usuario o correo" value="<?php echo htmlspecialchars($busqueda, ENT_QUOTES); ?>">
    <button class="boton boton--compacto" type="submit">Filtrar</button>
</form>

<!-- Tabla administrativa de usuarios. -->
<div class="tabla-contenedor">
    <table class="tabla-admin">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Telefono</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Registro</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellidos'], ENT_QUOTES); ?></td>
                    <td><?php echo htmlspecialchars($usuario['correo'], ENT_QUOTES); ?></td>
                    <td><?php echo htmlspecialchars($usuario['telefono'] ?? '-', ENT_QUOTES); ?></td>
                    <td><?php echo htmlspecialchars($usuario['rol_nombre'], ENT_QUOTES); ?></td>
                    <td><span class="estado estado--<?php echo htmlspecialchars($usuario['estado'], ENT_QUOTES); ?>"><?php echo htmlspecialchars(ucfirst($usuario['estado']), ENT_QUOTES); ?></span></td>
                    <td><?php echo htmlspecialchars($usuario['fecha_registro'], ENT_QUOTES); ?></td>
                    <td class="acciones">
                        <a href="<?php echo BASE_URL; ?>/admin_usuarios.php?accion=editar&id=<?php echo (int) $usuario['id_usuario']; ?>">Editar</a>
                        <form method="post" action="<?php echo BASE_URL; ?>/admin_usuarios.php?accion=eliminar">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">
                            <input type="hidden" name="id_usuario" value="<?php echo (int) $usuario['id_usuario']; ?>">
                            <button type="submit">Desactivar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($usuarios)): ?>
                <tr><td colspan="7">No hay usuarios para mostrar.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require BASE_PATH . '/vistas/layouts/footer.php'; ?>
