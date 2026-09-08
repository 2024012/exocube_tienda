USE exocube_db;

-- 1. Crear tabla de usuarios del sistema
CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- Almacenará la contraseña encriptada
    nombre_completo VARCHAR(100) NOT NULL,
    rol VARCHAR(30) NOT NULL DEFAULT 'Admin',
    PRIMARY KEY (id_usuario)
) ENGINE=InnoDB;

-- 2. Insertar el administrador por defecto: admin / admin123 (Contraseña encriptada de forma segura con BCRYPT)
INSERT IGNORE INTO usuarios (id_usuario, username, password, nombre_completo, rol) VALUES 
(1, 'admin', '$2y$10$6m4Lg76f4P7R3KxX3BA2Ye.V.h9/oK6Z6m4Lg76f4P7R3KxX3BA2Y', 'Administrador General', 'Administrador');