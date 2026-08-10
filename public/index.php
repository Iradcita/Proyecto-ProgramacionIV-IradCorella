<?php

require dirname(__DIR__) . '/config/config.php';

if (!usuarioAutenticado()) {
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

$usuario = $_SESSION['usuario'];
$tituloPagina = 'Inicio - NubeTurismo';

require BASE_PATH . '/vistas/layouts/header.php';
?>

<section class="panel-bienvenida">
    <h1>Bienvenido, <?php echo htmlspecialchars($usuario['nombre'], ENT_QUOTES); ?></h1>
    <p>Rol: <?php echo htmlspecialchars($usuario['rol_nombre'], ENT_QUOTES); ?></p>

    <?php if ($usuario['id_rol'] == 1): ?>
        <!-- Accesos principales del administrador para gestionar el sistema. -->
        <div class="admin-tarjetas">
            <a href="<?php echo BASE_URL; ?>/admin_destinos.php">
                <strong>Destinos</strong>
                <span>Crear, editar y desactivar destinos turisticos.</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/admin_hoteles.php">
                <strong>Hoteles</strong>
                <span>Administrar hospedajes, precios y habitaciones.</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/admin_actividades.php">
                <strong>Actividades</strong>
                <span>Gestionar tours, cupos, duracion y precios.</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/admin_usuarios.php">
                <strong>Usuarios</strong>
                <span>Asignar roles, estados y datos de acceso.</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/admin_reservaciones.php">
                <strong>Reservaciones</strong>
                <span>Crear, confirmar, editar o cancelar reservas.</span>
            </a>
        </div>
    <?php else: ?>
        <p>Próximamente: destinos, hoteles, actividades y tus reservaciones.</p>
    <?php endif; ?>
</section>

<?php require BASE_PATH . '/vistas/layouts/footer.php'; ?>
