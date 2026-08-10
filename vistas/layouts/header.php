<?php

$usuarioSesion = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($tituloPagina ?? 'NubeTurismo', ENT_QUOTES); ?></title>
    <link rel="stylesheet" href="<?php echo RECURSOS_URL; ?>/css/estilos.css">
</head>
<body>
<header class="cabecera">
    <div class="cabecera__contenedor">
        <a class="cabecera__marca" href="<?php echo BASE_URL; ?>/index.php">NubeTurismo</a>
        <nav class="cabecera__nav">
            <?php if ($usuarioSesion !== null): ?>
                <span class="cabecera__saludo">Hola, <?php echo htmlspecialchars($usuarioSesion['nombre'], ENT_QUOTES); ?></span>
                <?php if (esAdministrador()): ?>
                    <a href="<?php echo BASE_URL; ?>/admin_destinos.php">Destinos</a>
                    <a href="<?php echo BASE_URL; ?>/admin_hoteles.php">Hoteles</a>
                    <a href="<?php echo BASE_URL; ?>/admin_actividades.php">Actividades</a>
                    <a href="<?php echo BASE_URL; ?>/admin_usuarios.php">Usuarios</a>
                    <a href="<?php echo BASE_URL; ?>/admin_reservaciones.php">Reservaciones</a>
                    <a href="<?php echo BASE_URL; ?>/admin_reportes.php">Reportes</a>
                    <a href="<?php echo BASE_URL; ?>/admin_resenas.php">Resenas</a>
                    <a href="<?php echo BASE_URL; ?>/admin_bitacora.php">Bitacora</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>/destinos.php">Destinos</a>
                    <a href="<?php echo BASE_URL; ?>/hoteles.php">Hoteles</a>
                    <a href="<?php echo BASE_URL; ?>/actividades.php">Actividades</a>
                    <a href="<?php echo BASE_URL; ?>/reservar.php">Reservar</a>
                    <a href="<?php echo BASE_URL; ?>/mis_reservaciones.php">Mis reservas</a>
                <?php endif; ?>
                <a href="<?php echo BASE_URL; ?>/perfil.php">Perfil</a>
                <a href="<?php echo BASE_URL; ?>/logout.php">Cerrar sesión</a>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>/login.php">Iniciar sesión</a>
                <a href="<?php echo BASE_URL; ?>/registro.php">Crear cuenta</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="contenido">
