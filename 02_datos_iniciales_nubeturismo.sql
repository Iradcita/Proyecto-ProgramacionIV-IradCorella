-- ============================================================
-- nubeturismo - Sistema Web de Gestión Turística de Costa Rica
-- Este script crea los datos iniciales y datos de demostración
-- Ejecutar después de 01_creacion_bd_nubeturismo_db.sql
-- ============================================================

SET NAMES utf8mb4;
USE nubeturismo_db;

START TRANSACTION;

-- ------------------------------------------------------------
-- Crear roles base
-- ------------------------------------------------------------
INSERT INTO roles (id_rol, nombre, descripcion, estado) VALUES
    (1, 'Administrador', 'Acceso completo a la administración del sistema.', 1),
    (2, 'Cliente', 'Usuario que consulta destinos y realiza reservaciones.', 1);

-- ------------------------------------------------------------
-- Insertar provincias de Costa Rica
-- ------------------------------------------------------------
INSERT INTO provincias (id_provincia, nombre) VALUES
    (1, 'San José'),
    (2, 'Alajuela'),
    (3, 'Cartago'),
    (4, 'Heredia'),
    (5, 'Guanacaste'),
    (6, 'Puntarenas'),
    (7, 'Limón');

-- ------------------------------------------------------------
-- Usuarios de prueba
-- Contraseñas para uso local:
--   admin@nubeturismo.local   -> Admin123*
--   cliente@nubeturismo.local -> Cliente123*
-- Deben cambiarse antes de publicar el sistema.
-- ------------------------------------------------------------
INSERT INTO usuarios
    (id_usuario, id_rol, nombre, apellidos, correo, telefono,
     foto_url, password_hash, estado, fecha_registro)
VALUES
    -- EL hash lo generé con la función password_hash() de PHP, usando PASSWORD_BCRYPT y 12 rounds.
    -- asi password_hash('Admin123*', PASSWORD_BCRYPT, ['cost' => 12])

    (1, 1, 'Administrador', 'Sistema', 'admin@nubeturismo.local', '8888-0000',
     NULL, '$2y$12$uFrk1Sk6nkSGAwGPoqJBaODjfn8Y5XSPQ3S0b0YC3M.e90Xnvceta',
     'activo', CURRENT_TIMESTAMP),

    (2, 2, 'Cliente', 'Demostración', 'cliente@nubeturismo.local', '8888-1111',
     NULL, '$2y$12$7t0xB0HdtW9nA/12Y8JNr.Q4e3jAQEMbll9AWIcDnnKX26IhRByEG',
     'activo', CURRENT_TIMESTAMP);

-- ------------------------------------------------------------
-- Destinos de ejemplo
-- ------------------------------------------------------------
INSERT INTO destinos
    (id_destino, id_provincia, nombre, descripcion,
     imagen_principal, latitud, longitud, estado)
VALUES
    (1, 2, 'La Fortuna',
     'Destino con opciones de naturaleza, aventura y descanso.',
     'uploads/destinos/la-fortuna.jpg', NULL, NULL, 1),
    (2, 6, 'Manuel Antonio',
     'Destino con playas, senderos y actividades al aire libre.',
     'uploads/destinos/manuel-antonio.jpg', NULL, NULL, 1),
    (3, 7, 'Puerto Viejo',
     'Destino de ejemplo de ambiente costero',
     'uploads/destinos/puerto-viejo.jpg', NULL, NULL, 1);

-- ------------------------------------------------------------
-- Hoteles
-- ------------------------------------------------------------
INSERT INTO hoteles
    (id_hotel, id_destino, nombre, categoria, direccion, telefono,
     correo, precio_noche, cantidad_habitaciones, descripcion, imagen, estado)
VALUES
    (1, 1, 'Hotel Jardines del Volcán', 4, 'Centro de La Fortuna', '2479-1000',
     'reservas@jardinesvolcan.test', 65000.00, 30,
     'Hotel con el mejor servicio y calidad.',
     'uploads/hoteles/jardines-volcan.jpg', 1),
    (2, 2, 'Hotel Brisa del Pacífico', 4, 'Zona de Manuel Antonio', '2777-2000',
     'reservas@brisapacifico.test', 78000.00, 24,
     'Hotel brisas del pacífico, el mejor hotal de todos',
     'uploads/hoteles/brisa-pacifico.jpg', 1),
    (3, 3, 'Hotel Caribe Verde', 3, 'Centro de Puerto Viejo', '2750-3000',
     'reservas@caribeverde.test', 52000.00, 18,
     'Hotel con ambiente caribeño y excelente ubicación.',
     'uploads/hoteles/caribe-verde.jpg', 1);

-- ------------------------------------------------------------
-- Actividades ficticias para pruebas
-- ------------------------------------------------------------
INSERT INTO actividades
    (id_actividad, id_destino, nombre, descripcion, precio,
     duracion_minutos, cupo_maximo, imagen, estado)
VALUES
    (1, 1, 'Senderismo guiado',
     'Senderismo por los senderos del volcán Arenal con guía experto.',
     22000.00, 180, 20, 'uploads/actividades/senderismo.jpg', 1),
    (2, 1, 'Tour de puentes colgantes',
     'Puentes colgantes y vistas panorámicas de la selva tropical.',
     28000.00, 150, 18, 'uploads/actividades/puentes.jpg', 1),
    (3, 2, 'Tour de playa y senderos',
     'Exploración de las playas y senderos de Manuel Antonio.',
     25000.00, 240, 15, 'uploads/actividades/playa-senderos.jpg', 1),
    (4, 2, 'Paseo en kayak',
     'Riesgoso pero emocionante paseo en kayak por la costa de Manuel Antonio.',
     32000.00, 120, 12, 'uploads/actividades/kayak.jpg', 1),
    (5, 3, 'Tour cultural caribeño',
     'Tour de todas las islas del caribe, con degustación de comida típica.',
     18000.00, 120, 25, 'uploads/actividades/tour-cultural.jpg', 1);

-- ------------------------------------------------------------
-- Reservación completa de demostración
-- Dos noches de hotel: 65 000 x 2 = 130 000
-- Actividad para dos personas: 22 000 x 2 = 44 000
-- Total: 174 000
-- ------------------------------------------------------------
INSERT INTO reservaciones
    (id_reservacion, id_usuario, codigo, fecha_reserva, fecha_inicio,
     fecha_fin, cantidad_personas, estado, total, observaciones)
VALUES
    (1, 2, 'ITR-2026-000001', CURRENT_TIMESTAMP,
     '2026-08-10', '2026-08-12', 2, 'confirmada', 174000.00,
     'Reservación 1.');

INSERT INTO reservacion_hotel
    (id_reservacion_hotel, id_reservacion, id_hotel,
     cantidad_habitaciones, precio_noche_aplicado, subtotal)
VALUES
    (1, 1, 1, 1, 65000.00, 130000.00);

INSERT INTO reservacion_actividad
    (id_reservacion_actividad, id_reservacion, id_actividad,
     fecha_hora, cantidad_personas, precio_persona_aplicado, subtotal)
VALUES
    (1, 1, 1, '2026-08-11 08:00:00', 2, 22000.00, 44000.00);

-- ------------------------------------------------------------
-- Reseña y bitácora de demostración
-- ------------------------------------------------------------
INSERT INTO resenas_destinos
    (id_resena, id_usuario, id_destino, calificacion,
     comentario, estado, fecha)
VALUES
    (1, 2, 1, 5,
     'Comentario de demostración: excelente experiencia en La Fortuna.',
     'aprobada', CURRENT_TIMESTAMP);

INSERT INTO bitacora_acciones
    (id_bitacora, id_usuario, accion, tabla_afectada,
     id_registro_afectado, fecha)
VALUES
    (1, 1, 'CREAR_DATOS_INICIALES', 'sistema', NULL,
     CURRENT_TIMESTAMP);

COMMIT;
