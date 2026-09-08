USE exocube_db;

-- 1. Agregar columnas de imágenes a la tabla productos
ALTER TABLE productos 
ADD COLUMN imagen_frontal VARCHAR(255) NULL AFTER informacion_prod,
ADD COLUMN imagen_lateral VARCHAR(255) NULL AFTER imagen_frontal,
ADD COLUMN imagen_trasera VARCHAR(255) NULL AFTER imagen_lateral;

-- 2. Actualizar las imágenes y datos del ID 1 (Pelota de fútbol)
UPDATE productos 
SET imagen_frontal = 'product_images/52.jpg', -- Ajusta si tienes nombres específicos
    imagen_lateral = 'product_images/52.jpg', 
    imagen_trasera = 'product_images/52.jpg',
    stock = 15,
    disponibilidad = 1
WHERE id_producto = 1;

-- 3. Actualizar las imágenes y datos del ID 2 (Frasco de burbujas)
UPDATE productos 
SET imagen_frontal = 'product_images/57.png', 
    imagen_lateral = 'product_images/57.png', 
    imagen_trasera = 'product_images/57.png',
    stock = 40,
    disponibilidad = 1
WHERE id_producto = 2;

-- 4. Actualizar las imágenes y datos del ID 3 (Balde de cubos)
UPDATE productos 
SET imagen_frontal = 'product_images/85.jpg', 
    imagen_lateral = 'product_images/85.jpg', 
    imagen_trasera = 'product_images/85.jpg',
    stock = 20,
    disponibilidad = 1
WHERE id_producto = 3;