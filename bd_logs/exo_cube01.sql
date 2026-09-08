-- 1. Creación de la Base de Datos si no existe
CREATE DATABASE IF NOT EXISTS exocube_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE exocube_db;

-- 2. Creación de la Tabla: categorias
-- Se debe crear primero para permitir la relación de llave foránea en productos
CREATE TABLE IF NOT EXISTS categorias (
    id_categoria INT AUTO_INCREMENT,
    nombre_cat VARCHAR(50) NOT NULL,
    descripcion TEXT NULL,
    PRIMARY KEY (id_categoria)
) ENGINE=InnoDB;

-- 3. Creación de la Tabla: productos (Modificada)
CREATE TABLE IF NOT EXISTS productos (
    id_producto INT AUTO_INCREMENT,
    nombre_prod VARCHAR(100) NOT NULL,
    descripcion VARCHAR(255) NULL, -- Modificado: Breve reseña del producto
    informacion_prod TEXT NULL,     -- Nuevo: Información detallada y técnica
    precio DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    stock INT NOT NULL DEFAULT 0,
    disponibilidad TINYINT(1) NOT NULL DEFAULT 1, -- 1 = Disponible, 0 = Agotado
    id_categoria INT NOT NULL,
    PRIMARY KEY (id_producto),
    CONSTRAINT fk_productos_categorias 
        FOREIGN KEY (id_categoria) 
        REFERENCES categorias (id_categoria)
        ON DELETE RESTRICT 
        ON UPDATE CASCADE
) ENGINE=InnoDB;