USE exocube_db;

-- 1. Agregar el campo de fecha de ingreso a la tabla de productos
ALTER TABLE productos 
ADD COLUMN fecha_ingreso DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER id_categoria;

-- 2. Actualizar las fechas de algunos productos de prueba para simular que ingresaron hace más de 20 días
-- (Esto nos servirá para probar el sistema de 14 días)
UPDATE productos SET fecha_ingreso = NOW() - INTERVAL 20 DAY WHERE id_producto IN (1, 2, 3, 4, 5, 6);

-- 3. Asegurar que los productos más recientes tengan la fecha de hoy
UPDATE productos SET fecha_ingreso = NOW() WHERE id_producto > 6;