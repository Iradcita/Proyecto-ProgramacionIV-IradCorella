-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-07-2026 a las 06:23:23
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `nubeturismo_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividades`
--

CREATE TABLE `actividades` (
  `id_actividad` int(11) NOT NULL,
  `id_destino` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` varchar(1000) NOT NULL,
  `precio` decimal(12,2) NOT NULL,
  `duracion_minutos` int(11) NOT NULL,
  `cupo_maximo` int(11) NOT NULL,
  `imagen` varchar(500) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1
) ;

--
-- Volcado de datos para la tabla `actividades`
--

INSERT INTO `actividades` (`id_actividad`, `id_destino`, `nombre`, `descripcion`, `precio`, `duracion_minutos`, `cupo_maximo`, `imagen`, `estado`) VALUES
(1, 1, 'Senderismo guiado', 'Senderismo por los senderos del volcán Arenal con guía experto.', 22000.00, 180, 20, NULL, 1),
(2, 1, 'Tour de puentes colgantes', 'Puentes colgantes y vistas panorámicas de la selva tropical.', 28000.00, 150, 18, 'uploads/actividades/puentes.jpg', 1),
(3, 2, 'Tour de playa y senderos', 'Exploración de las playas y senderos de Manuel Antonio.', 25000.00, 240, 15, NULL, 1),
(4, 2, 'Paseo en kayak', 'Riesgoso pero emocionante paseo en kayak por la costa de Manuel Antonio.', 32000.00, 120, 12, 'uploads/actividades/kayak.jpg', 1),
(5, 3, 'Tour cultural caribeño', 'Tour de todas las islas del caribe, con degustación de comida típica.', 18000.00, 120, 25, NULL, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bitacora_acciones`
--

CREATE TABLE `bitacora_acciones` (
  `id_bitacora` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `accion` varchar(100) NOT NULL,
  `tabla_afectada` varchar(100) DEFAULT NULL,
  `id_registro_afectado` int(11) DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `bitacora_acciones`
--

INSERT INTO `bitacora_acciones` (`id_bitacora`, `id_usuario`, `accion`, `tabla_afectada`, `id_registro_afectado`, `fecha`) VALUES
(1, 1, 'CREAR_DATOS_INICIALES', 'sistema', NULL, '2026-07-26 22:15:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `destinos`
--

CREATE TABLE `destinos` (
  `id_destino` int(11) NOT NULL,
  `id_provincia` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` varchar(1000) NOT NULL,
  `imagen_principal` varchar(500) DEFAULT NULL,
  `latitud` decimal(10,7) DEFAULT NULL,
  `longitud` decimal(10,7) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1
) ;

--
-- Volcado de datos para la tabla `destinos`
--

INSERT INTO `destinos` (`id_destino`, `id_provincia`, `nombre`, `descripcion`, `imagen_principal`, `latitud`, `longitud`, `estado`) VALUES
(1, 2, 'La Fortuna', 'Destino con opciones de naturaleza, aventura y descanso.', 'uploads/destinos/la-fortuna.jpg', 10.4678000, -84.6427000, 1),
(2, 6, 'Manuel Antonio', 'Destino con playas, senderos y actividades al aire libre.', 'uploads/destinos/manuel-antonio.jpg', 9.3920000, -84.1370000, 1),
(3, 7, 'Puerto Viejo', 'Destino de ejemplo de ambiente costero', 'uploads/destinos/puerto-viejo.jpg', 9.6560000, -82.7548000, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hoteles`
--

CREATE TABLE `hoteles` (
  `id_hotel` int(11) NOT NULL,
  `id_destino` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `categoria` tinyint(4) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `telefono` varchar(25) DEFAULT NULL,
  `correo` varchar(190) DEFAULT NULL,
  `precio_noche` decimal(12,2) NOT NULL,
  `cantidad_habitaciones` int(11) NOT NULL,
  `descripcion` varchar(1000) NOT NULL,
  `imagen` varchar(500) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1
) ;

--
-- Volcado de datos para la tabla `hoteles`
--

INSERT INTO `hoteles` (`id_hotel`, `id_destino`, `nombre`, `categoria`, `direccion`, `telefono`, `correo`, `precio_noche`, `cantidad_habitaciones`, `descripcion`, `imagen`, `estado`) VALUES
(1, 1, 'Hotel Jardines del Volcán', 4, 'Centro de La Fortuna', '2479-1000', 'reservas@jardinesvolcan.test', 65000.00, 30, 'Hotel con el mejor servicio y calidad.', 'uploads/hoteles/jardines-volcan.jpg', 1),
(2, 2, 'Hotel Brisa del Pacífico', 4, 'Zona de Manuel Antonio', '2777-2000', 'reservas@brisapacifico.test', 78000.00, 24, 'Hotel brisas del pacífico, el mejor hotal de todos', 'uploads/hoteles/brisa-pacifico.jpg', 1),
(3, 3, 'Hotel Caribe Verde', 3, 'Centro de Puerto Viejo', '2750-3000', 'reservas@caribeverde.test', 52000.00, 18, 'Hotel con ambiente caribeño y excelente ubicación.', 'uploads/hoteles/caribe-verde.jpg', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE `password_resets` (
  `id_reset` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `token_hash` varchar(255) NOT NULL,
  `expira_en` datetime NOT NULL,
  `usado` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `provincias`
--

CREATE TABLE `provincias` (
  `id_provincia` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `provincias`
--

INSERT INTO `provincias` (`id_provincia`, `nombre`) VALUES
(2, 'Alajuela'),
(3, 'Cartago'),
(5, 'Guanacaste'),
(4, 'Heredia'),
(7, 'Limón'),
(6, 'Puntarenas'),
(1, 'San José');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resenas_destinos`
--

CREATE TABLE `resenas_destinos` (
  `id_resena` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_destino` int(11) NOT NULL,
  `calificacion` tinyint(4) NOT NULL,
  `comentario` varchar(1000) DEFAULT NULL,
  `estado` enum('pendiente','aprobada','rechazada') NOT NULL DEFAULT 'pendiente',
  `fecha` datetime NOT NULL DEFAULT current_timestamp()
) ;

--
-- Volcado de datos para la tabla `resenas_destinos`
--

INSERT INTO `resenas_destinos` (`id_resena`, `id_usuario`, `id_destino`, `calificacion`, `comentario`, `estado`, `fecha`) VALUES
(1, 2, 1, 5, 'Comentario de demostración: excelente experiencia en La Fortuna.', 'aprobada', '2026-07-26 22:15:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservaciones`
--

CREATE TABLE `reservaciones` (
  `id_reservacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `codigo` varchar(30) NOT NULL,
  `fecha_reserva` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `cantidad_personas` int(11) NOT NULL,
  `estado` enum('pendiente','confirmada','cancelada','completada') NOT NULL DEFAULT 'pendiente',
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `observaciones` varchar(1000) DEFAULT NULL
) ;

--
-- Volcado de datos para la tabla `reservaciones`
--

INSERT INTO `reservaciones` (`id_reservacion`, `id_usuario`, `codigo`, `fecha_reserva`, `fecha_inicio`, `fecha_fin`, `cantidad_personas`, `estado`, `total`, `observaciones`) VALUES
(1, 2, 'ITR-2026-000001', '2026-07-26 22:15:48', '2026-08-10', '2026-08-12', 2, 'confirmada', 174000.00, 'Reservación 1.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservacion_actividad`
--

CREATE TABLE `reservacion_actividad` (
  `id_reservacion_actividad` int(11) NOT NULL,
  `id_reservacion` int(11) NOT NULL,
  `id_actividad` int(11) NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `cantidad_personas` int(11) NOT NULL,
  `precio_persona_aplicado` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL
) ;

--
-- Volcado de datos para la tabla `reservacion_actividad`
--

INSERT INTO `reservacion_actividad` (`id_reservacion_actividad`, `id_reservacion`, `id_actividad`, `fecha_hora`, `cantidad_personas`, `precio_persona_aplicado`, `subtotal`) VALUES
(1, 1, 1, '2026-08-11 08:00:00', 2, 22000.00, 44000.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservacion_hotel`
--

CREATE TABLE `reservacion_hotel` (
  `id_reservacion_hotel` int(11) NOT NULL,
  `id_reservacion` int(11) NOT NULL,
  `id_hotel` int(11) NOT NULL,
  `cantidad_habitaciones` int(11) NOT NULL,
  `precio_noche_aplicado` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL
) ;

--
-- Volcado de datos para la tabla `reservacion_hotel`
--

INSERT INTO `reservacion_hotel` (`id_reservacion_hotel`, `id_reservacion`, `id_hotel`, `cantidad_habitaciones`, `precio_noche_aplicado`, `subtotal`) VALUES
(1, 1, 1, 1, 65000.00, 130000.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1
) ;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre`, `descripcion`, `estado`) VALUES
(1, 'Administrador', 'Acceso completo a la administración del sistema.', 1),
(2, 'Cliente', 'Usuario que consulta destinos y realiza reservaciones.', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(150) NOT NULL,
  `correo` varchar(190) NOT NULL,
  `telefono` varchar(25) DEFAULT NULL,
  `foto_url` varchar(500) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `estado` enum('activo','inactivo','bloqueado') NOT NULL DEFAULT 'activo',
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `id_rol`, `nombre`, `apellidos`, `correo`, `telefono`, `foto_url`, `password_hash`, `estado`, `fecha_registro`) VALUES
(1, 1, 'Administrador', 'Sistema', 'admin@nubeturismo.local', '8888-0000', NULL, '$2y$12$uFrk1Sk6nkSGAwGPoqJBaODjfn8Y5XSPQ3S0b0YC3M.e90Xnvceta', 'activo', '2026-07-26 22:15:48'),
(2, 2, 'Cliente', 'Demostración', 'cliente@nubeturismo.local', '8888-1111', NULL, '$2y$12$7t0xB0HdtW9nA/12Y8JNr.Q4e3jAQEMbll9AWIcDnnKX26IhRByEG', 'activo', '2026-07-26 22:15:48');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `actividades`
--
ALTER TABLE `actividades`
  ADD PRIMARY KEY (`id_actividad`),
  ADD UNIQUE KEY `uq_actividades_destino_nombre` (`id_destino`,`nombre`),
  ADD KEY `idx_actividades_destino` (`id_destino`),
  ADD KEY `idx_actividades_nombre` (`nombre`),
  ADD KEY `idx_actividades_precio` (`precio`),
  ADD KEY `idx_actividades_estado` (`estado`);

--
-- Indices de la tabla `bitacora_acciones`
--
ALTER TABLE `bitacora_acciones`
  ADD PRIMARY KEY (`id_bitacora`),
  ADD KEY `idx_bitacora_usuario` (`id_usuario`),
  ADD KEY `idx_bitacora_fecha` (`fecha`),
  ADD KEY `idx_bitacora_tabla_registro` (`tabla_afectada`,`id_registro_afectado`),
  ADD KEY `idx_bitacora_accion` (`accion`);

--
-- Indices de la tabla `destinos`
--
ALTER TABLE `destinos`
  ADD PRIMARY KEY (`id_destino`),
  ADD UNIQUE KEY `uq_destinos_provincia_nombre` (`id_provincia`,`nombre`),
  ADD KEY `idx_destinos_provincia` (`id_provincia`),
  ADD KEY `idx_destinos_nombre` (`nombre`),
  ADD KEY `idx_destinos_estado` (`estado`);

--
-- Indices de la tabla `hoteles`
--
ALTER TABLE `hoteles`
  ADD PRIMARY KEY (`id_hotel`),
  ADD UNIQUE KEY `uq_hoteles_destino_nombre` (`id_destino`,`nombre`),
  ADD KEY `idx_hoteles_destino` (`id_destino`),
  ADD KEY `idx_hoteles_nombre` (`nombre`),
  ADD KEY `idx_hoteles_categoria` (`categoria`),
  ADD KEY `idx_hoteles_precio` (`precio_noche`),
  ADD KEY `idx_hoteles_estado` (`estado`);

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id_reset`),
  ADD UNIQUE KEY `uq_password_resets_token` (`token_hash`),
  ADD KEY `idx_password_resets_usuario` (`id_usuario`),
  ADD KEY `idx_password_resets_expiracion` (`expira_en`),
  ADD KEY `idx_password_resets_disponible` (`usado`,`expira_en`);

--
-- Indices de la tabla `provincias`
--
ALTER TABLE `provincias`
  ADD PRIMARY KEY (`id_provincia`),
  ADD UNIQUE KEY `uq_provincias_nombre` (`nombre`);

--
-- Indices de la tabla `resenas_destinos`
--
ALTER TABLE `resenas_destinos`
  ADD PRIMARY KEY (`id_resena`),
  ADD UNIQUE KEY `uq_resenas_usuario_destino` (`id_usuario`,`id_destino`),
  ADD KEY `idx_resenas_destino` (`id_destino`),
  ADD KEY `idx_resenas_estado` (`estado`),
  ADD KEY `idx_resenas_fecha` (`fecha`);

--
-- Indices de la tabla `reservaciones`
--
ALTER TABLE `reservaciones`
  ADD PRIMARY KEY (`id_reservacion`),
  ADD UNIQUE KEY `uq_reservaciones_codigo` (`codigo`),
  ADD KEY `idx_reservaciones_usuario` (`id_usuario`),
  ADD KEY `idx_reservaciones_estado` (`estado`),
  ADD KEY `idx_reservaciones_fecha_reserva` (`fecha_reserva`),
  ADD KEY `idx_reservaciones_periodo` (`fecha_inicio`,`fecha_fin`);

--
-- Indices de la tabla `reservacion_actividad`
--
ALTER TABLE `reservacion_actividad`
  ADD PRIMARY KEY (`id_reservacion_actividad`),
  ADD UNIQUE KEY `uq_reservacion_actividad` (`id_reservacion`,`id_actividad`),
  ADD KEY `idx_reservacion_actividad_reservacion` (`id_reservacion`),
  ADD KEY `idx_reservacion_actividad_actividad` (`id_actividad`),
  ADD KEY `idx_reservacion_actividad_fecha` (`fecha_hora`);

--
-- Indices de la tabla `reservacion_hotel`
--
ALTER TABLE `reservacion_hotel`
  ADD PRIMARY KEY (`id_reservacion_hotel`),
  ADD UNIQUE KEY `uq_reservacion_hotel_reservacion` (`id_reservacion`),
  ADD KEY `idx_reservacion_hotel_hotel` (`id_hotel`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`),
  ADD UNIQUE KEY `uq_roles_nombre` (`nombre`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `uq_usuarios_correo` (`correo`),
  ADD KEY `idx_usuarios_rol` (`id_rol`),
  ADD KEY `idx_usuarios_nombre` (`nombre`,`apellidos`),
  ADD KEY `idx_usuarios_estado` (`estado`),
  ADD KEY `idx_usuarios_fecha_registro` (`fecha_registro`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `actividades`
--
ALTER TABLE `actividades`
  MODIFY `id_actividad` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `bitacora_acciones`
--
ALTER TABLE `bitacora_acciones`
  MODIFY `id_bitacora` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `destinos`
--
ALTER TABLE `destinos`
  MODIFY `id_destino` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `hoteles`
--
ALTER TABLE `hoteles`
  MODIFY `id_hotel` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id_reset` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `provincias`
--
ALTER TABLE `provincias`
  MODIFY `id_provincia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `resenas_destinos`
--
ALTER TABLE `resenas_destinos`
  MODIFY `id_resena` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reservaciones`
--
ALTER TABLE `reservaciones`
  MODIFY `id_reservacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reservacion_actividad`
--
ALTER TABLE `reservacion_actividad`
  MODIFY `id_reservacion_actividad` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reservacion_hotel`
--
ALTER TABLE `reservacion_hotel`
  MODIFY `id_reservacion_hotel` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `actividades`
--
ALTER TABLE `actividades`
  ADD CONSTRAINT `fk_actividades_destinos` FOREIGN KEY (`id_destino`) REFERENCES `destinos` (`id_destino`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `bitacora_acciones`
--
ALTER TABLE `bitacora_acciones`
  ADD CONSTRAINT `fk_bitacora_usuarios` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `destinos`
--
ALTER TABLE `destinos`
  ADD CONSTRAINT `fk_destinos_provincias` FOREIGN KEY (`id_provincia`) REFERENCES `provincias` (`id_provincia`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `hoteles`
--
ALTER TABLE `hoteles`
  ADD CONSTRAINT `fk_hoteles_destinos` FOREIGN KEY (`id_destino`) REFERENCES `destinos` (`id_destino`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `fk_password_resets_usuarios` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `resenas_destinos`
--
ALTER TABLE `resenas_destinos`
  ADD CONSTRAINT `fk_resenas_destinos` FOREIGN KEY (`id_destino`) REFERENCES `destinos` (`id_destino`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_resenas_usuarios` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `reservaciones`
--
ALTER TABLE `reservaciones`
  ADD CONSTRAINT `fk_reservaciones_usuarios` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `reservacion_actividad`
--
ALTER TABLE `reservacion_actividad`
  ADD CONSTRAINT `fk_reservacion_actividad_actividades` FOREIGN KEY (`id_actividad`) REFERENCES `actividades` (`id_actividad`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reservacion_actividad_reservaciones` FOREIGN KEY (`id_reservacion`) REFERENCES `reservaciones` (`id_reservacion`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `reservacion_hotel`
--
ALTER TABLE `reservacion_hotel`
  ADD CONSTRAINT `fk_reservacion_hotel_hoteles` FOREIGN KEY (`id_hotel`) REFERENCES `hoteles` (`id_hotel`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reservacion_hotel_reservaciones` FOREIGN KEY (`id_reservacion`) REFERENCES `reservaciones` (`id_reservacion`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_roles` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
