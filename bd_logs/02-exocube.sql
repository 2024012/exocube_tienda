USE exocube_db;

-- 1. Agregar soporte de encendido/apagado (Toggle) para locales físicos
ALTER TABLE locales_tienda ADD COLUMN activo TINYINT(1) NOT NULL DEFAULT 1;

-- 2. Agregar soporte de encendido/apagado (Toggle) para redes sociales y enlaces
ALTER TABLE enlaces_contacto ADD COLUMN activo TINYINT(1) NOT NULL DEFAULT 1;
