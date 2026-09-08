USE exocube_db;

-- Agregar la columna de celular y dirección de entrega exacta a la tabla pedidos
ALTER TABLE pedidos ADD COLUMN cliente_celular VARCHAR(15) NULL AFTER cliente_correo;
ALTER TABLE pedidos ADD COLUMN direccion_entrega VARCHAR(255) NULL AFTER tipo_entrega;