USE exocube_db;

-- Agregar la columna 'activo' a la tabla de políticas del portal
ALTER TABLE politicas_portal 
ADD COLUMN activo TINYINT(1) NOT NULL DEFAULT 1 AFTER fecha_actualizacion;