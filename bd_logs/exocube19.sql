USE exocube_db;

-- Agregar columna para almacenar la ruta de la captura de pago en la tabla pedidos
ALTER TABLE pedidos 
ADD COLUMN comprobante_pago VARCHAR(255) NULL AFTER total_compra;