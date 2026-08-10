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
        <!-- Accesos principales del cliente para consultar y reservar. -->
        <div class="admin-tarjetas">
            <a href="<?php echo BASE_URL; ?>/destinos.php">
                <strong>Destinos</strong>
                <span>Consulta lugares turisticos activos de Costa Rica.</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/hoteles.php">
                <strong>Hoteles</strong>
                <span>Busca hospedajes por destino, categoria o precio.</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/actividades.php">
                <strong>Actividades</strong>
                <span>Encuentra tours y experiencias disponibles.</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/reservar.php">
                <strong>Reservar</strong>
                <span>Crea una solicitud con hotel, fechas y actividades.</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/mis_reservaciones.php">
                <strong>Mis reservas</strong>
                <span>Revisa el estado y total de tus reservaciones.</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/perfil.php">
                <strong>Mi perfil</strong>
                <span>Actualiza datos, foto y contrasena.</span>
            </a>
        </div>
    <?php endif; ?>
</section>

<?php require BASE_PATH . '/vistas/layouts/footer.php'; ?>
