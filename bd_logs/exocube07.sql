USE exocube_db;

-- 1. Agregar la columna de descuento a la tabla de productos
ALTER TABLE productos 
ADD COLUMN descuento INT NOT NULL DEFAULT 0 AFTER precio;

-- 2. Configurar descuentos en algunos productos para la sección de promociones
UPDATE productos SET descuento = 20 WHERE id_producto = 10; -- MUÑECA BRATZ con 20%
UPDATE productos SET descuento = 15 WHERE id_producto = 11; -- SET DE LEGOS STAR WARS con 15%
UPDATE productos SET descuento = 10 WHERE id_producto = 12; -- ARDUINO UNO con 10%
UPDATE productos SET descuento = 30 WHERE id_producto = 17; -- AUDIFONOS GAMER con 30%
UPDATE productos SET descuento = 25 WHERE id_producto = 13; -- TERMO TRANSPARENTE con 25%
UPDATE productos SET descuento = 15 WHERE id_producto = 3;  -- BALDE DE CUBOS con 15%