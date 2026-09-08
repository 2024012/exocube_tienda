USE exocube_db;

-- 1. Crear la tabla de enlaces de contacto si no existe
CREATE TABLE IF NOT EXISTS enlaces_contacto (
    id_contacto INT AUTO_INCREMENT,
    plataforma VARCHAR(50) NOT NULL, -- Ej: "WhatsApp", "Instagram"
    enlace VARCHAR(255) NOT NULL,     -- El link URL o mailto directo
    etiqueta VARCHAR(100) NOT NULL,   -- El texto visible (ej. "966085432")
    icono_url VARCHAR(100) NOT NULL,  -- Ruta del icono (ej. "img/whatsapp.png")
    PRIMARY KEY (id_contacto)
) ENGINE=InnoDB;

-- 2. Inserción de tus 3 enlaces por defecto para exo_cube
INSERT INTO enlaces_contacto (plataforma, enlace, etiqueta, icono_url) VALUES 
('WhatsApp', 'https://wa.me/51966085432', '966085432', 'img/whatsapp.png'),
('Instagram', 'https://instagram.com/exo_cube', 'exo_cube', 'img/social.png'),
('Correo', 'mailto:ex1cube@gmail.com', 'ex1cube@gmail.com', 'img/correo-electronico.png');