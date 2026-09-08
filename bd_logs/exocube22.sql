USE exocube_db;

-- 1. Insertar el nuevo canal de Facebook de exo_cube (con su imagen facebook.png)
INSERT INTO enlaces_contacto (plataforma, enlace, etiqueta, icono_url) VALUES 
('Facebook', 'https://facebook.com/exo_cube', 'exo_cube', 'img/facebook.png');

-- 2. Insertar el segundo número de WhatsApp de soporte para rotación
INSERT INTO enlaces_contacto (plataforma, enlace, etiqueta, icono_url) VALUES 
('WhatsApp', 'https://wa.me/51955123456', '955123456', 'img/whatsapp.png');