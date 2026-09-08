USE exocube_db;

-- 1. Crear la tabla de información general del negocio
CREATE TABLE IF NOT EXISTS informacion_tienda (
    id_info INT AUTO_INCREMENT,
    quienes_somos TEXT NOT NULL,
    experiencia TEXT NOT NULL,
    direccion TEXT NOT NULL,
    google_maps_iframe TEXT NULL, -- Almacenará el tag <iframe> completo de Google Maps
    horario_atencion VARCHAR(100) NOT NULL,
    PRIMARY KEY (id_info)
) ENGINE=InnoDB;

-- 2. Insertar los datos por defecto basados en tu maquetación de Tacna
INSERT INTO informacion_tienda (id_info, quienes_somos, experiencia, direccion, google_maps_iframe, horario_atencion) VALUES 
(
    1, 
    'Somos una tienda minorista de regalos y juguetes con trato directo al público, operando fielmente de forma física durante más de 5 años en la ciudad de Tacna.', 
    'Nos enfocamos en ofrecer juguetes y regalos diversos traídos de importadoras para el público en general, padres de familia y personas buscando regalos.', 
    'Calle San Martín Nro. 123 (Frente a la Plaza de Armas), Tacna - Perú.', 
    '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3791.956740612662!2d-70.2529946851128!3d-18.01334498770744!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x9136c968f9aef10f%3A0x7a30cf7f0fc6c8e!2sPlaza%20de%20Armas%20de%20Tacna!5e0!3m2!1ses-419!2spe!4v1680000000000!5m2!1ses-419!2spe" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>', 
    'LUNES A SABADO 9:00 A 18:00'
);