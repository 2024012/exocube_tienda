USE exocube_db;

-- 1. Tabla Cabecera: Registro de envío único (Almacena solo correo y fecha)
CREATE TABLE IF NOT EXISTS consultas_envios (
    id_envio INT AUTO_INCREMENT,
    correo_feedback VARCHAR(150) NOT NULL,
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_envio)
) ENGINE=InnoDB;

-- 2. Tabla Detalle: Almacena de forma ilimitada cada consulta con SU PROPIO archivo adjunto
CREATE TABLE IF NOT EXISTS consultas_detalles (
    id_detalle INT AUTO_INCREMENT,
    id_envio INT NOT NULL,
    consulta_texto TEXT NOT NULL,
    datos_extra VARCHAR(255) NULL,
    archivo_adjunto VARCHAR(255) NULL, -- Movido aquí para permitir un archivo por consulta
    PRIMARY KEY (id_detalle),
    CONSTRAINT fk_envio_consultas_detalles 
        FOREIGN KEY (id_envio) 
        REFERENCES consultas_envios (id_envio) 
        ON DELETE CASCADE
) ENGINE=InnoDB;