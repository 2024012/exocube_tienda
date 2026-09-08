USE exocube_db;

-- 1. Corregir los subtotales en la tabla detalle_pedidos para el Pedido 2
UPDATE detalle_pedidos 
SET subtotal = 17.00 
WHERE id_detalle = 2 AND id_pedido = 2;

UPDATE detalle_pedidos 
SET subtotal = 30.00 
WHERE id_detalle = 3 AND id_pedido = 2;

UPDATE detalle_pedidos 
SET subtotal = 50.40 
WHERE id_detalle = 4 AND id_pedido = 2;

-- 2. Corregir el costo total de la compra en la tabla pedidos para el Pedido 2
UPDATE pedidos 
SET total_compra = 97.40 
WHERE id_pedido = 2;