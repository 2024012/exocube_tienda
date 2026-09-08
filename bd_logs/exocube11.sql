USE exocube_db;

-- 1. Eliminar los campos de local de la tabla principal para limpiarla
ALTER TABLE informacion_tienda 
DROP COLUMN direccion,
DROP COLUMN google_maps_iframe,
DROP COLUMN horario_atencion;

-- 2. Crear la nueva tabla de locales (relación 1:N con informacion_tienda)
CREATE TABLE IF NOT EXISTS locales_tienda (
    id_local INT AUTO_INCREMENT,
    id_info INT NOT NULL,
    nombre_local VARCHAR(100) NOT NULL, -- Ej: "Sucursal Tacna Centro", "Sucursal Cono Sur"
    direccion TEXT NOT NULL,
    google_maps_iframe TEXT NULL,
    horario_atencion VARCHAR(100) NOT NULL,
    PRIMARY KEY (id_local),
    FOREIGN KEY (id_info) REFERENCES informacion_tienda(id_info) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 3. Insertar la información física de 2 locales de prueba para exo_cube
INSERT INTO locales_tienda (id_info, nombre_local, direccion, google_maps_iframe, horario_atencion) VALUES 
(
    1, 
    'SUCURSAL TACNA CENTRO', 
    'Calle San Martín Nro. 123 (Frente a la Plaza de Armas), Tacna - Perú.', 
    '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3791.956740612662!2d-70.2529946851128!3d-18.01334498770744!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x9136c968f9aef10f%3A0x7a30cf7f0fc6c8e!2sPlaza%20de%20Armas%20de%20Tacna!5e0!3m2!1ses-419!2spe!4v1680000000000!5m2!1ses-419!2spe" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>', 
    'LUNES A SABADO 9:00 A 18:00'
),
(
    1, 
    'SUCURSAL CONO SUR', 
    'Av. Municipal Nro. 456 (A media cuadra del Óvalo de la Cultura), Nuevo Tacna.', 
    '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3791.245265487854!2d-70.2312456!3d-18.0315482!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x9136cf988888888f%3A0x8888888888888888!2sAsoc.+De+Vivienda+Villa+El+Salvador!5e0!3m2!1ses-419!2spe!4v1680000000000!5m2!1ses-419!2spe" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>', 
    'LUNES A SABADO 10:00 A 19:00'
);