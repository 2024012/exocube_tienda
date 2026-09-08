USE exocube_db;

-- Crear tabla de registro de copias de seguridad de exo_cube
CREATE TABLE IF NOT EXISTS backups (
    id_backup INT AUTO_INCREMENT,
    nombre_archivo VARCHAR(150) NOT NULL,
    ruta_archivo VARCHAR(255) NOT NULL,
    tipo_backup VARCHAR(30) NOT NULL, -- "Manual" o "Automático"
    fecha_generacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    peso_archivo VARCHAR(30) NOT NULL, -- Ej: "45 KB", "1.2 MB"
    PRIMARY KEY (id_backup)
) ENGINE=InnoDB;