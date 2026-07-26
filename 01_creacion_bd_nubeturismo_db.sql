-- ============================================================
-- nubeturismo_db - Script para crear la base de datos
-- Irad Corella
-- Para este avance el script de base de datos esta completo 
-- ============================================================

-- Este script elimina las tablas existentes si ya se habia creado la base antes
-- Para poder hacer el esquema desde cero.

SET NAMES utf8mb4;
SET SQL_MODE = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- Crear y usar la base de datos nubeturismo_db
CREATE DATABASE IF NOT EXISTS nubeturismo_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE nubeturismo_db;

-- esto hace que las FKs no se verifiquen, para poder eliminar las tablas sin problemas
SET FOREIGN_KEY_CHECKS = 0;

-- Eliminamos las tablas si ya existían, para poder crear desde cero.
DROP TABLE IF EXISTS bitacora_acciones;
DROP TABLE IF EXISTS resenas_destinos;
DROP TABLE IF EXISTS reservacion_actividad;
DROP TABLE IF EXISTS reservacion_hotel;
DROP TABLE IF EXISTS reservaciones;
DROP TABLE IF EXISTS actividades;
DROP TABLE IF EXISTS hoteles;
DROP TABLE IF EXISTS destinos;
DROP TABLE IF EXISTS provincias;
DROP TABLE IF EXISTS password_resets;
DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS roles;

-- Volvemos a activar la verificación de FKs
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- 1. Tabla de ROLES, para definir los permisos de los usuarios (Admin, cliente) y si estan activos
-- ============================================================
CREATE TABLE roles (
    id_rol INT AUTO_INCREMENT, -- ID, se genera automáticamente
    nombre VARCHAR(255) NOT NULL,
    descripcion VARCHAR(255) NULL,
    estado TINYINT(1) NOT NULL DEFAULT 1, -- 1 = activo, 0 = inactivo

    CONSTRAINT pk_roles PRIMARY KEY (id_rol), 
    CONSTRAINT uq_roles_nombre UNIQUE (nombre), -- nombre de rol debe ser único
    CONSTRAINT chk_roles_estado CHECK (estado IN (0, 1)) -- solo puede ser 0 o 1
) ENGINE = InnoDB;

-- ============================================================
-- 2. Tabla de USUARIOS, almacena el rol, correo, contraseña y estado del usuario
-- ============================================================
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT,
    id_rol INT NOT NULL,   
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(150) NOT NULL,
    correo VARCHAR(190) NOT NULL,
    telefono VARCHAR(25) NULL, 
    foto_url VARCHAR(500) NULL, -- URL de la foto del usuario, puede ser nulo
    password_hash VARCHAR(255) NOT NULL,
    -- estado del usuario: activo, inactivo o bloqueado
    estado ENUM('activo', 'inactivo', 'bloqueado') NOT NULL DEFAULT 'activo',
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT pk_usuarios PRIMARY KEY (id_usuario),
    CONSTRAINT uq_usuarios_correo UNIQUE (correo), -- correo debe ser único
    CONSTRAINT fk_usuarios_roles
        FOREIGN KEY (id_rol)
        REFERENCES roles (id_rol)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    -- Índices para optimizar consultas frecuentes por rol, nombre, estado y fecha de registro
    INDEX idx_usuarios_rol (id_rol),
    INDEX idx_usuarios_nombre (nombre, apellidos),
    INDEX idx_usuarios_estado (estado),
    INDEX idx_usuarios_fecha_registro (fecha_registro)
) ENGINE = InnoDB;

-- ============================================================
-- 3. TOKENS PARA RESTABLECIMIENTO DE CONTRASEÑA
-- ============================================================
CREATE TABLE password_resets (
    id_reset INT  AUTO_INCREMENT,
    id_usuario INT  NOT NULL,
    token_hash VARCHAR(255) NOT NULL,
    expira_en DATETIME NOT NULL,
    usado TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT pk_password_resets PRIMARY KEY (id_reset),
    CONSTRAINT uq_password_resets_token UNIQUE (token_hash),
    CONSTRAINT chk_password_resets_usado CHECK (usado IN (0, 1)),
    CONSTRAINT fk_password_resets_usuarios
        FOREIGN KEY (id_usuario)
        REFERENCES usuarios (id_usuario)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    INDEX idx_password_resets_usuario (id_usuario),
    INDEX idx_password_resets_expiracion (expira_en),
    INDEX idx_password_resets_disponible (usado, expira_en)
) ENGINE = InnoDB;

-- ============================================================
-- 4. Tabla de PROVINCIAS, conecta con destinos y hoteles, para filtrar por ubicación
-- ============================================================
CREATE TABLE provincias (
    id_provincia INT AUTO_INCREMENT,
    nombre VARCHAR(255) NOT NULL,

    CONSTRAINT pk_provincias PRIMARY KEY (id_provincia),
    CONSTRAINT uq_provincias_nombre UNIQUE (nombre)
) ENGINE = InnoDB;

-- ============================================================
-- 5. Tabla de DESTINOS TURÍSTICOS, conecta con provincias, hoteles y actividades, para mostrar información de cada destino
-- ============================================================
CREATE TABLE destinos (
    id_destino INT AUTO_INCREMENT,
    id_provincia INT NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    descripcion VARCHAR(1000) NOT NULL,
    imagen_principal VARCHAR(500) NULL, -- URL de la imagen principal del destino, puede ser nulo
    latitud DECIMAL(10, 7) NULL, -- Latitud geográfica del destino, puede ser nulo
    longitud DECIMAL(10, 7) NULL, -- Longitud geográfica del destino, puede ser nulo
    estado TINYINT(1) NOT NULL DEFAULT 1, -- 1 = activo, 0 = inactivo

    CONSTRAINT pk_destinos PRIMARY KEY (id_destino),
    CONSTRAINT uq_destinos_provincia_nombre UNIQUE (id_provincia, nombre),
    -- Validaciones para latitud y longitud, si no son nulas deben estar en rangos válidos
    -- Se necesita para mostrar la ubicación en mapa con API de Google Maps
    CONSTRAINT chk_destinos_latitud
        CHECK (latitud IS NULL OR latitud BETWEEN -90.0000000 AND 90.0000000),
    CONSTRAINT chk_destinos_longitud
        CHECK (longitud IS NULL OR longitud BETWEEN -180.0000000 AND 180.0000000),
    CONSTRAINT chk_destinos_estado CHECK (estado IN (0, 1)),
    CONSTRAINT fk_destinos_provincias
        FOREIGN KEY (id_provincia)
        REFERENCES provincias (id_provincia)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    -- Índices para acelerar consultas frecuentes por provincia, nombre y estado
    INDEX idx_destinos_provincia (id_provincia),
    INDEX idx_destinos_nombre (nombre),
    INDEX idx_destinos_estado (estado)
) ENGINE = InnoDB;

-- ============================================================
-- 6. Tabla de HOTELES
-- ============================================================
CREATE TABLE hoteles (
    id_hotel INT AUTO_INCREMENT,
    id_destino INT NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    categoria TINYINT NOT NULL, -- Categoría del hotel (1 a 5 estrellas)
    direccion VARCHAR(255) NOT NULL,
    telefono VARCHAR(25) NULL,
    correo VARCHAR(190) NULL,
    precio_noche DECIMAL(12, 2) NOT NULL, -- Precio por noche en colones
    cantidad_habitaciones INT NOT NULL,
    descripcion VARCHAR(1000) NOT NULL,
    imagen VARCHAR(500) NULL,   -- URL de la imagen del hotel, puede ser nulo
    estado TINYINT(1) NOT NULL DEFAULT 1, -- 1 = activo, 0 = inactivo

    CONSTRAINT pk_hoteles PRIMARY KEY (id_hotel),
    CONSTRAINT uq_hoteles_destino_nombre UNIQUE (id_destino, nombre),
    -- Validaciones para categoría, precio, cantidad de habitaciones y estado
    CONSTRAINT chk_hoteles_categoria CHECK (categoria BETWEEN 1 AND 5),
    CONSTRAINT chk_hoteles_precio CHECK (precio_noche >= 0),
    CONSTRAINT chk_hoteles_habitaciones CHECK (cantidad_habitaciones >= 0),
    CONSTRAINT chk_hoteles_estado CHECK (estado IN (0, 1)),
    CONSTRAINT fk_hoteles_destinos
        FOREIGN KEY (id_destino)
        REFERENCES destinos (id_destino)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    -- Índices para acelerar consultas por destino, nombre, categoría, precio y estado
    INDEX idx_hoteles_destino (id_destino),
    INDEX idx_hoteles_nombre (nombre),
    INDEX idx_hoteles_categoria (categoria),
    INDEX idx_hoteles_precio (precio_noche),
    INDEX idx_hoteles_estado (estado)
) ENGINE = InnoDB;

-- ============================================================
-- 7. Tabla de ACTIVIDADES TURÍSTICAS, conecta con destinos, para mostrar información de cada actividad
-- ============================================================
CREATE TABLE actividades (
    id_actividad INT AUTO_INCREMENT,
    id_destino INT NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    descripcion VARCHAR(1000) NOT NULL,
    precio DECIMAL(12, 2) NOT NULL,
    duracion_minutos INT NOT NULL,
    cupo_maximo INT  NOT NULL,
    imagen VARCHAR(500) NULL,
    estado TINYINT(1) NOT NULL DEFAULT 1, -- 1 = activo, 0 = inactivo

    CONSTRAINT pk_actividades PRIMARY KEY (id_actividad),
    CONSTRAINT uq_actividades_destino_nombre UNIQUE (id_destino, nombre),
    CONSTRAINT chk_actividades_precio CHECK (precio >= 0),
    CONSTRAINT chk_actividades_duracion CHECK (duracion_minutos > 0),
    CONSTRAINT chk_actividades_cupo CHECK (cupo_maximo > 0), --  el cupo máximo debe ser mayor a 0
    CONSTRAINT chk_actividades_estado CHECK (estado IN (0, 1)),
    CONSTRAINT fk_actividades_destinos
        FOREIGN KEY (id_destino)
        REFERENCES destinos (id_destino)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    INDEX idx_actividades_destino (id_destino),
    INDEX idx_actividades_nombre (nombre),
    INDEX idx_actividades_precio (precio),
    INDEX idx_actividades_estado (estado)
) ENGINE = InnoDB;

-- ============================================================
-- 8.Tabla RESERVACIONES, conecta con usuarios, hoteles y actividades, para registrar las reservaciones de los clientes
-- ============================================================
CREATE TABLE reservaciones (
    id_reservacion INT AUTO_INCREMENT,
    id_usuario INT  NOT NULL,
    codigo VARCHAR(30) NOT NULL,
    fecha_reserva DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- fecha y hora en que se realizó la reservación, por default es la fecha actual
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    cantidad_personas INT  NOT NULL,
    estado ENUM('pendiente', 'confirmada', 'cancelada', 'completada') -- estado de la reservación, por default es 'pendiente'
        NOT NULL DEFAULT 'pendiente',
    total DECIMAL(12, 2) NOT NULL DEFAULT 0.00, -- total de la reservación, calculado como la suma de los costos de hotel y actividades
    observaciones VARCHAR(1000) NULL, -- comentarios adicionales de la reservación, puede ser null

    CONSTRAINT pk_reservaciones PRIMARY KEY (id_reservacion),
    CONSTRAINT uq_reservaciones_codigo UNIQUE (codigo),
    CONSTRAINT chk_reservaciones_fechas CHECK (fecha_fin >= fecha_inicio), -- la fecha de fin debe ser mayor o igual a la fecha de inicio
    CONSTRAINT chk_reservaciones_personas CHECK (cantidad_personas > 0),
    CONSTRAINT chk_reservaciones_total CHECK (total >= 0), -- el total debe ser mayor o igual a 0
    CONSTRAINT chk_reservaciones_estado CHECK (estado IN ('pendiente', 'confirmada', 'cancelada', 'completada')), -- las opciones válidas para el estado
    CONSTRAINT fk_reservaciones_usuarios
        FOREIGN KEY (id_usuario)
        REFERENCES usuarios (id_usuario)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    INDEX idx_reservaciones_usuario (id_usuario),
    INDEX idx_reservaciones_estado (estado),
    INDEX idx_reservaciones_fecha_reserva (fecha_reserva),
    INDEX idx_reservaciones_periodo (fecha_inicio, fecha_fin)
) ENGINE = InnoDB;

-- ============================================================
-- 9. DETALLE DE HOTEL DE UNA RESERVACIÓN
-- Relación 1 a 0..1: una reservación puede tener como máximo un hotel.
-- ============================================================
CREATE TABLE reservacion_hotel (
    id_reservacion_hotel INT AUTO_INCREMENT,
    id_reservacion INT  NOT NULL,
    id_hotel INT  NOT NULL,
    cantidad_habitaciones INT  NOT NULL,
    precio_noche_aplicado DECIMAL(12, 2) NOT NULL,
    subtotal DECIMAL(12, 2) NOT NULL,

    CONSTRAINT pk_reservacion_hotel PRIMARY KEY (id_reservacion_hotel),
    CONSTRAINT uq_reservacion_hotel_reservacion UNIQUE (id_reservacion),
    CONSTRAINT chk_reservacion_hotel_habitaciones CHECK (cantidad_habitaciones > 0),
    CONSTRAINT chk_reservacion_hotel_precio CHECK (precio_noche_aplicado >= 0),
    CONSTRAINT chk_reservacion_hotel_subtotal CHECK (subtotal >= 0),
    CONSTRAINT fk_reservacion_hotel_reservaciones
        FOREIGN KEY (id_reservacion)
        REFERENCES reservaciones (id_reservacion)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT fk_reservacion_hotel_hoteles
        FOREIGN KEY (id_hotel)
        REFERENCES hoteles (id_hotel)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    INDEX idx_reservacion_hotel_hotel (id_hotel)
) ENGINE = InnoDB;

-- ============================================================
-- 10. ACTIVIDADES INCLUIDAS EN UNA RESERVACIÓN
-- Relación 1 a N: una reservación puede tener varias actividades.
-- ============================================================
CREATE TABLE reservacion_actividad (
    id_reservacion_actividad INT AUTO_INCREMENT,
    id_reservacion INT  NOT NULL,
    id_actividad INT  NOT NULL,
    fecha_hora DATETIME NOT NULL,
    cantidad_personas INT  NOT NULL,
    precio_persona_aplicado DECIMAL(12, 2) NOT NULL,
    subtotal DECIMAL(12, 2) NOT NULL,

    CONSTRAINT pk_reservacion_actividad PRIMARY KEY (id_reservacion_actividad),
    CONSTRAINT uq_reservacion_actividad UNIQUE (id_reservacion, id_actividad), -- una reservación no puede tener la misma actividad más de una vez
    CONSTRAINT chk_reservacion_actividad_personas CHECK (cantidad_personas > 0),
    CONSTRAINT chk_reservacion_actividad_precio CHECK (precio_persona_aplicado >= 0),
    CONSTRAINT chk_reservacion_actividad_subtotal CHECK (subtotal >= 0),
    CONSTRAINT fk_reservacion_actividad_reservaciones
        FOREIGN KEY (id_reservacion)
        REFERENCES reservaciones (id_reservacion)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT fk_reservacion_actividad_actividades
        FOREIGN KEY (id_actividad)
        REFERENCES actividades (id_actividad)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    INDEX idx_reservacion_actividad_reservacion (id_reservacion),
    INDEX idx_reservacion_actividad_actividad (id_actividad),
    INDEX idx_reservacion_actividad_fecha (fecha_hora)
) ENGINE = InnoDB;

-- ============================================================
-- 11. RESEÑAS DE DESTINOS
-- Un usuario solamente puede dejar una reseña por destino.
-- ============================================================
CREATE TABLE resenas_destinos (
    id_resena INT AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    id_destino INT NOT NULL,
    calificacion TINYINT NOT NULL,-- calificación del destino, de 1 a 5
    comentario VARCHAR(1000) NULL,
    estado ENUM('pendiente', 'aprobada', 'rechazada') -- estado de la reseña, por default es 'pendiente'
        NOT NULL DEFAULT 'pendiente',
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT pk_resenas_destinos PRIMARY KEY (id_resena),
    CONSTRAINT uq_resenas_usuario_destino UNIQUE (id_usuario, id_destino),
    CONSTRAINT chk_resenas_calificacion CHECK (calificacion BETWEEN 1 AND 5),
    CONSTRAINT fk_resenas_usuarios
        FOREIGN KEY (id_usuario)
        REFERENCES usuarios (id_usuario)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_resenas_destinos
        FOREIGN KEY (id_destino)
        REFERENCES destinos (id_destino)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    INDEX idx_resenas_destino (id_destino),
    INDEX idx_resenas_estado (estado),
    INDEX idx_resenas_fecha (fecha)
) ENGINE = InnoDB;

-- ============================================================
-- 12. BITÁCORA DE ACCIONES
-- Registra las acciones realizadas por los usuarios
-- ============================================================
CREATE TABLE bitacora_acciones (
    id_bitacora INT  AUTO_INCREMENT,
    id_usuario INT  NOT NULL,
    accion VARCHAR(100) NOT NULL,-- acción realizada por el usuario
    tabla_afectada VARCHAR(100) NULL, -- tabla de la base de datos afectada por la acción, puede ser nulo
    id_registro_afectado INT  NULL, -- id del registro afectado por la acción
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT pk_bitacora_acciones PRIMARY KEY (id_bitacora),
    CONSTRAINT fk_bitacora_usuarios
        FOREIGN KEY (id_usuario)
        REFERENCES usuarios (id_usuario)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    INDEX idx_bitacora_usuario (id_usuario),
    INDEX idx_bitacora_fecha (fecha),
    INDEX idx_bitacora_tabla_registro (tabla_afectada, id_registro_afectado),
    INDEX idx_bitacora_accion (accion)
) ENGINE = InnoDB;
