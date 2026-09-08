USE exocube_db;

-- Agregar las columnas de correo y nombre del cliente a la tabla pedidos
ALTER TABLE pedidos 
ADD COLUMN cliente_correo VARCHAR(100) NULL AFTER fecha_pedido,
ADD COLUMN cliente_nombre VARCHAR(100) NULL AFTER cliente_correo;