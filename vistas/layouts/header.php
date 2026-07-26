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
                <a href="<?php echo BASE_URL; ?>/logout.php">Cerrar sesión</a>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>/login.php">Iniciar sesión</a>
                <a href="<?php echo BASE_URL; ?>/registro.php">Crear cuenta</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="contenido">
