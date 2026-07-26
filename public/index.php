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
        <p>Próximamente: panel de administración (destinos, hoteles, actividades, reservaciones, reportes).</p>
    <?php else: ?>
        <p>Próximamente: destinos, hoteles, actividades y tus reservaciones.</p>
    <?php endif; ?>
</section>

<?php require BASE_PATH . '/vistas/layouts/footer.php'; ?>
